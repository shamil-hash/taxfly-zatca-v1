<?php

namespace App\Services;

use DOMDocument;
use DOMElement;
use RuntimeException;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use RobRichards\XMLSecLibs\XMLSecurityKey;
use RobRichards\XMLSecLibs\XMLSecurityDSig;
use OpenSSLAsymmetricKey;

use Endroid\QrCode\QrCode;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\ErrorCorrectionLevel;
use Endroid\QrCode\Writer\PngWriter;

class ZatcaService
{
    protected array $certificate;
    protected string $certPem;
    protected string $certDer;
    protected ?\OpenSSLAsymmetricKey $privateKey = null;
    protected ?string $issuerDn = null;
    protected ?string $serialHex = null;
    protected string $sandboxUrl;
    
    // Namespace constants
    protected const NS_UBL = 'urn:oasis:names:specification:ubl:schema:xsd:Invoice-2';
    protected const NS_CAC = 'urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2';
    protected const NS_CBC = 'urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2';
    protected const NS_EXT = 'urn:oasis:names:specification:ubl:schema:xsd:CommonExtensionComponents-2';
    protected const NS_DS = 'http://www.w3.org/2000/09/xmldsig#';
    protected const NS_XADES = 'http://uri.etsi.org/01903/v1.3.2#';
    
    // Logging context
    protected array $logContext = ['service' => 'ZatcaService'];

    public function __construct()
    {
        $this->sandboxUrl = config('zatca.sandbox_url', 'https://gw-fatoora.zatca.gov.sa/e-invoicing/sandbox');
        Log::info('ZatcaService initialized', $this->logContext);
        $this->loadActiveCertificate();
    }

    /**
     * Load active certificate from database
     */
    protected function loadActiveCertificate(): void
    {
        Log::info('Loading active certificate from database', $this->logContext);
        
        $activeId = DB::table('zatca_settings')->value('active_certificate_id');
        if (!$activeId) {
            $error = 'No active certificate configured in zatca_settings';
            Log::error($error, $this->logContext);
            throw new RuntimeException($error);
        }

        Log::debug("Active certificate ID: {$activeId}", $this->logContext);

        $row = DB::table('zatca_certificates')
            ->where('id', $activeId)
            ->whereNull('deleted_at')
            ->where('is_active', true)
            ->first([
                'certificate_path',
                'private_key_path',
                'certificate_password',
                'serial_number',
                'issuer',
                'issuer_cert_path'
            ]);

        if (!$row) {
            $error = 'Active certificate not found or inactive';
            Log::error($error, $this->logContext);
            throw new RuntimeException($error);
        }

        $this->certificate = [
            'certificate_path' => $row->certificate_path,
            'private_key_path' => $row->private_key_path,
            'certificate_password' => $row->certificate_password,
            'serial_number' => $row->serial_number,
            'issuer' => $row->issuer,
            'issuer_cert_path' => $row->issuer_cert_path ?? null,
        ];

        Log::info('Certificate loaded from database', array_merge($this->logContext, [
            'certificate_path' => $this->certificate['certificate_path'],
            'has_issuer_cert' => !empty($this->certificate['issuer_cert_path'])
        ]));

        $this->loadCertificateFiles();
        $this->parseCertificateInfo();
    }

    /**
     * Load certificate and private key files with proper path resolution
     */
    protected function loadCertificateFiles(): void
    {
        Log::info('Loading certificate files', $this->logContext);
        
        // Resolve paths
        $certPath = $this->resolveFilePath($this->certificate['certificate_path']);
        $keyPath = $this->resolveFilePath($this->certificate['private_key_path']);

        Log::debug("Resolved certificate path: {$certPath}", $this->logContext);
        Log::debug("Resolved key path: {$keyPath}", $this->logContext);

        if (!is_readable($certPath)) {
            $error = "Certificate file not readable: {$certPath}";
            Log::error($error, $this->logContext);
            throw new RuntimeException($error);
        }

        $this->certPem = file_get_contents($certPath);
        if ($this->certPem === false) {
            $error = 'Failed to read certificate file';
            Log::error($error, $this->logContext);
            throw new RuntimeException($error);
        }

        Log::info('Certificate file loaded successfully', $this->logContext);

        $this->certDer = $this->convertPemToDer($this->certPem);
        Log::debug('Certificate converted to DER format', $this->logContext);

        if (!is_readable($keyPath)) {
            $error = "Private key file not readable: {$keyPath}";
            Log::error($error, $this->logContext);
            throw new RuntimeException($error);
        }

        $keyPem = file_get_contents($keyPath);
        if ($keyPem === false) {
            $error = 'Failed to read private key file';
            Log::error($error, $this->logContext);
            throw new RuntimeException($error);
        }

        Log::info('Private key file loaded successfully', $this->logContext);

        $privateKey = openssl_pkey_get_private(
            $keyPem,
            $this->certificate['certificate_password'] ?: ''
        );
        
        if ($privateKey === false) {
            $error = 'Failed to load private key (check password)';
            Log::error($error, $this->logContext);
            throw new RuntimeException($error);
        }

        $this->privateKey = $privateKey;
        Log::info('Private key loaded successfully', $this->logContext);
    }

    /**
     * Resolve file path - handle both relative and absolute paths
     */
    protected function resolveFilePath(string $path): string
    {
        // If it's already an absolute path, return as is
        if (preg_match('/^\//', $path) || preg_match('/^[a-zA-Z]:\\\\/', $path)) {
            return $path;
        }

        // Remove any leading "storage/" or "app/" prefixes to avoid duplication
        $cleanPath = preg_replace(['#^storage/#', '#^app/#'], '', $path);
        
        // If path contains 'certs/', assume it's relative to storage directory
        if (strpos($cleanPath, 'certs/') === 0) {
            return storage_path($cleanPath);
        }

        // Otherwise, assume it's relative to storage app directory
        return storage_path('app/' . ltrim($cleanPath, '/'));
    }

    /**
     * Parse certificate information
     */
    protected function parseCertificateInfo(): void
    {
        Log::info('Parsing certificate information', $this->logContext);
        
        $parsed = openssl_x509_parse($this->certPem);
        if (!$parsed) {
            $error = 'Failed to parse certificate';
            Log::error($error, $this->logContext);
            throw new RuntimeException($error);
        }

        $this->issuerDn = $this->formatDistinguishedName($parsed['issuer'] ?? []);
        $this->serialHex = $this->convertSerialToHex(
            $parsed['serialNumber'] ?? null,
            $parsed['serialNumberHex'] ?? null
        );

        Log::info('Certificate information parsed', array_merge($this->logContext, [
            'issuer' => $this->issuerDn,
            'serial_hex' => $this->serialHex,
            'valid_from' => date('Y-m-d H:i:s', $parsed['validFrom_time_t'] ?? 0),
            'valid_to' => date('Y-m-d H:i:s', $parsed['validTo_time_t'] ?? 0)
        ]));
    }

    /**************************************************************************
     * --------------------------- UBL Generator ------------------------------
     **************************************************************************/

    public function generateUblInvoice(array $invoice): string
    {
        Log::info('Generating UBL invoice', array_merge($this->logContext, [
            'invoice_id' => $invoice['id'] ?? 'unknown',
            'invoice_number' => $invoice['number'] ?? 'unknown'
        ]));

        $doc = new DOMDocument('1.0', 'UTF-8');
        $doc->preserveWhiteSpace = false;
        $doc->formatOutput = false;

        // Root Invoice element
        $invoiceEl = $doc->createElementNS(self::NS_UBL, 'Invoice');
        $doc->appendChild($invoiceEl);

        // Declare namespaces
        $invoiceEl->setAttributeNS('http://www.w3.org/2000/xmlns/', 'xmlns:cac', self::NS_CAC);
        $invoiceEl->setAttributeNS('http://www.w3.org/2000/xmlns/', 'xmlns:cbc', self::NS_CBC);
        $invoiceEl->setAttributeNS('http://www.w3.org/2000/xmlns/', 'xmlns:ext', self::NS_EXT);
        $invoiceEl->setAttributeNS('http://www.w3.org/2000/xmlns/', 'xmlns:ds', self::NS_DS);
        $invoiceEl->setAttributeNS('http://www.w3.org/2000/xmlns/', 'xmlns:xades', self::NS_XADES);

        // UBL Extensions (required by ZATCA)
        $extEl = $doc->createElementNS(self::NS_EXT, 'ext:UBLExtensions');
        $invoiceEl->appendChild($extEl);

        // Add basic invoice information
        $this->addBasicInvoiceElements($doc, $invoiceEl, $invoice);
        
        // Add supplier and customer information
        $this->addPartyInformation($doc, $invoiceEl, $invoice);
        
        // Add invoice lines
        $this->addInvoiceLines($doc, $invoiceEl, $invoice);
        
        // Add tax and monetary totals
        $this->addTotals($doc, $invoiceEl, $invoice);

        $xml = $doc->saveXML();
        Log::info('UBL invoice generated successfully', array_merge($this->logContext, [
            'invoice_id' => $invoice['id'] ?? 'unknown',
            'xml_length' => strlen($xml)
        ]));

        return $xml;
    }

    /**
     * Add basic invoice elements to the XML
     */
    protected function addBasicInvoiceElements(DOMDocument $doc, DOMElement $invoiceEl, array $invoice): void
    {
        $elements = [
            'cbc:UBLVersionID' => '2.1',
            'cbc:CustomizationID' => $invoice['customization_id'] ?? 'urn:fdc:pe:ubl:customization:psvi:1',
            'cbc:ProfileID' => 'reporting:1.0', // ZATCA required
            'cbc:ID' => $invoice['number'] ?? ('INV-' . ($invoice['id'] ?? time())),
            'cbc:IssueDate' => $invoice['issue_date'] ?? date('Y-m-d'),
            'cbc:IssueTime' => date('H:i:s'), // ZATCA required
            'cbc:InvoiceTypeCode' => $invoice['type_code'] ?? '388',
            'cbc:DocumentCurrencyCode' => $invoice['currency'] ?? 'SAR',
            'cbc:TaxCurrencyCode' => $invoice['currency'] ?? 'SAR',
        ];

        foreach ($elements as $tag => $value) {
            $invoiceEl->appendChild($doc->createElementNS(self::NS_CBC, $tag, $value));
        }
    }

    /**
     * Add supplier and customer information to the XML
     */
    protected function addPartyInformation(DOMDocument $doc, DOMElement $invoiceEl, array $invoice): void
    {
        // Supplier Party (ZATCA requires specific structure)
        $supplier = $invoice['supplier'] ?? [];
        $supplierParty = $doc->createElementNS(self::NS_CAC, 'cac:AccountingSupplierParty');
        $party = $this->createPartyElement($doc, $supplier);
        $supplierParty->appendChild($party);
        $invoiceEl->appendChild($supplierParty);

        // Customer Party (same structure as supplier)
        $customer = $invoice['customer'] ?? [];
        $customerParty = $doc->createElementNS(self::NS_CAC, 'cac:AccountingCustomerParty');
        $custParty = $this->createPartyElement($doc, $customer);
        $customerParty->appendChild($custParty);
        $invoiceEl->appendChild($customerParty);
    }

    /**
     * Create a party element for supplier or customer
     */
    protected function createPartyElement(DOMDocument $doc, array $partyData): DOMElement
    {
        $party = $doc->createElementNS(self::NS_CAC, 'cac:Party');
        
        // Party Identification (VAT number)
        if (!empty($partyData['vat_number'])) {
            $partyIdentification = $doc->createElementNS(self::NS_CAC, 'cac:PartyIdentification');
            $idElement = $doc->createElementNS(self::NS_CBC, 'cbc:ID', $partyData['vat_number']);
            $idElement->setAttribute('schemeID', 'VAT');
            $partyIdentification->appendChild($idElement);
            $party->appendChild($partyIdentification);
        }

        // Party Name
        $partyName = $doc->createElementNS(self::NS_CAC, 'cac:PartyName');
        $partyName->appendChild($doc->createElementNS(self::NS_CBC, 'cbc:Name', $partyData['name'] ?? ''));
        $party->appendChild($partyName);

        // Postal Address (required by ZATCA)
        if (!empty($partyData['address'])) {
            $postal = $doc->createElementNS(self::NS_CAC, 'cac:PostalAddress');
            $postal->appendChild($doc->createElementNS(self::NS_CBC, 'cbc:StreetName', $partyData['address']));
            
            if (!empty($partyData['city'])) {
                $postal->appendChild($doc->createElementNS(self::NS_CBC, 'cbc:CountrySubentity', $partyData['city']));
            }
            
            $postal->appendChild($doc->createElementNS(self::NS_CBC, 'cbc:Country', $partyData['country'] ?? 'SA'));
            $party->appendChild($postal);
        }

        // Party Tax Scheme
        if (!empty($partyData['vat_number'])) {
            $tax = $doc->createElementNS(self::NS_CAC, 'cac:PartyTaxScheme');
            $tax->appendChild($doc->createElementNS(self::NS_CBC, 'cbc:CompanyID', $partyData['vat_number']));
            $taxScheme = $doc->createElementNS(self::NS_CAC, 'cac:TaxScheme');
            $taxScheme->appendChild($doc->createElementNS(self::NS_CBC, 'cbc:ID', 'VAT'));
            $tax->appendChild($taxScheme);
            $party->appendChild($tax);
        }

        return $party;
    }

    /**
     * Add invoice lines to the XML
     */
    protected function addInvoiceLines(DOMDocument $doc, DOMElement $invoiceEl, array $invoice): void
    {
        foreach ($invoice['lines'] ?? [] as $idx => $line) {
            $lineEl = $doc->createElementNS(self::NS_CAC, 'cac:InvoiceLine');
            $lineEl->appendChild($doc->createElementNS(self::NS_CBC, 'cbc:ID', (string)($idx + 1)));
            $lineEl->appendChild($doc->createElementNS(self::NS_CBC, 'cbc:InvoicedQuantity', (string)($line['quantity'] ?? 1)));
            $lineEl->appendChild($doc->createElementNS(self::NS_CBC, 'cbc:LineExtensionAmount', number_format((float)($line['line_extension_amount'] ?? 0), 2, '.', '')));

            // Item
            $item = $doc->createElementNS(self::NS_CAC, 'cac:Item');
            $item->appendChild($doc->createElementNS(self::NS_CBC, 'cbc:Description', $line['description'] ?? ''));
            $lineEl->appendChild($item);

            // Price
            $price = $doc->createElementNS(self::NS_CAC, 'cac:Price');
            $price->appendChild($doc->createElementNS(self::NS_CBC, 'cbc:PriceAmount', number_format((float)($line['unit_price'] ?? 0), 2, '.', '')));
            $lineEl->appendChild($price);

            // Tax Total per line
            $lineTaxTotal = $doc->createElementNS(self::NS_CAC, 'cac:TaxTotal');
            $lineTaxTotal->appendChild($doc->createElementNS(self::NS_CBC, 'cbc:TaxAmount', number_format((float)($line['tax_amount'] ?? 0), 2, '.', '')));

            $lineTaxSubtotal = $doc->createElementNS(self::NS_CAC, 'cac:TaxSubtotal');
            $lineTaxSubtotal->appendChild($doc->createElementNS(self::NS_CBC, 'cbc:TaxableAmount', number_format((float)($line['line_extension_amount'] ?? 0), 2, '.', '')));
            $lineTaxSubtotal->appendChild($doc->createElementNS(self::NS_CBC, 'cbc:TaxAmount', number_format((float)($line['tax_amount'] ?? 0), 2, '.', '')));

            $lineTaxCategory = $doc->createElementNS(self::NS_CAC, 'cac:TaxCategory');
            $lineTaxCategory->appendChild($doc->createElementNS(self::NS_CBC, 'cbc:Percent', number_format((float)($line['tax_percent'] ?? 15), 2, '.', '')));

            $lineTaxScheme = $doc->createElementNS(self::NS_CAC, 'cac:TaxScheme');
            $lineTaxScheme->appendChild($doc->createElementNS(self::NS_CBC, 'cbc:ID', 'VAT'));
            $lineTaxCategory->appendChild($lineTaxScheme);

            $lineTaxSubtotal->appendChild($lineTaxCategory);
            $lineTaxTotal->appendChild($lineTaxSubtotal);
            $lineEl->appendChild($lineTaxTotal);

            $invoiceEl->appendChild($lineEl);
        }
    }

    /**
     * Add tax and monetary totals to the XML
     */
    protected function addTotals(DOMDocument $doc, DOMElement $invoiceEl, array $invoice): void
    {
        // Document-level TaxTotal
        $taxTotal = $doc->createElementNS(self::NS_CAC, 'cac:TaxTotal');
        $taxTotal->appendChild($doc->createElementNS(self::NS_CBC, 'cbc:TaxAmount', number_format((float)($invoice['tax_total'] ?? 0), 2, '.', '')));

        $taxSubtotal = $doc->createElementNS(self::NS_CAC, 'cac:TaxSubtotal');
        $taxSubtotal->appendChild($doc->createElementNS(self::NS_CBC, 'cbc:TaxableAmount', number_format((float)($invoice['line_extension_total'] ?? 0), 2, '.', '')));
        $taxSubtotal->appendChild($doc->createElementNS(self::NS_CBC, 'cbc:TaxAmount', number_format((float)($invoice['tax_total'] ?? 0), 2, '.', '')));

        $taxCategory = $doc->createElementNS(self::NS_CAC, 'cac:TaxCategory');
        $taxCategory->appendChild($doc->createElementNS(self::NS_CBC, 'cbc:Percent', number_format((float)($invoice['tax_percent'] ?? 15), 2, '.', '')));

        $taxScheme = $doc->createElementNS(self::NS_CAC, 'cac:TaxScheme');
        $taxScheme->appendChild($doc->createElementNS(self::NS_CBC, 'cbc:ID', 'VAT'));
        $taxCategory->appendChild($taxScheme);

        $taxSubtotal->appendChild($taxCategory);
        $taxTotal->appendChild($taxSubtotal);
        $invoiceEl->appendChild($taxTotal);

        // LegalMonetaryTotal
        $legalMonetaryTotal = $doc->createElementNS(self::NS_CAC, 'cac:LegalMonetaryTotal');
        $legalMonetaryTotal->appendChild($doc->createElementNS(self::NS_CBC, 'cbc:LineExtensionAmount', number_format((float)($invoice['line_extension_total'] ?? 0), 2, '.', '')));
        $legalMonetaryTotal->appendChild($doc->createElementNS(self::NS_CBC, 'cbc:TaxExclusiveAmount', number_format((float)($invoice['line_extension_total'] ?? 0), 2, '.', '')));
        $legalMonetaryTotal->appendChild($doc->createElementNS(self::NS_CBC, 'cbc:TaxInclusiveAmount', number_format((float)($invoice['legal_monetary_total']['payableAmount'] ?? 0), 2, '.', '')));
        $legalMonetaryTotal->appendChild($doc->createElementNS(self::NS_CBC, 'cbc:PayableAmount', number_format((float)($invoice['legal_monetary_total']['payableAmount'] ?? 0), 2, '.', '')));
        $invoiceEl->appendChild($legalMonetaryTotal);
    }

    /**************************************************************************
     * --------------------------- DB mapping --------------------------------
     **************************************************************************/

    public function buildInvoiceArray(int $invoiceId): array
    {
        Log::info('Building invoice array from database', array_merge($this->logContext, [
            'invoice_id' => $invoiceId
        ]));

        $invoiceRow = DB::table('invoicedatas')->where('id', $invoiceId)->first();
        if (!$invoiceRow) {
            $error = "Invoice #{$invoiceId} not found in invoicedatas";
            Log::error($error, $this->logContext);
            throw new RuntimeException($error);
        }

        $lineRows = DB::table('new_buyproducts')
            ->where('invoice_id', $invoiceId)
            ->get();

        $lines = [];
        $lineExtensionTotal = 0.0;
        $taxTotal = 0.0;

        foreach ($lineRows as $row) {
            $qty = (float)($row->quantity ?? 1);
            $unitPrice = (float)($row->price ?? 0);
            $lineTotal = $qty * $unitPrice;
            $vatAmount = (float)($row->vat_amount ?? 0);

            $lines[] = [
                'description'            => $row->product_name ?? '',
                'quantity'               => $qty,
                'unit_price'             => $unitPrice,
                'line_extension_amount'  => $lineTotal,
                'tax_amount'             => $vatAmount,
                'tax_percent'            => 15, // Default VAT rate for Saudi Arabia
            ];

            $lineExtensionTotal += $lineTotal;
            $taxTotal += $vatAmount;
        }

        // Get supplier and customer information
        $supplier = $this->getSupplierInformation($invoiceRow);
        $customer = $this->getCustomerInformation($invoiceRow);

        // Calculate totals
        $invoiceTaxTotal = $invoiceRow->vat_total ?? $taxTotal;
        $payable = $invoiceRow->total_amount ?? ($lineExtensionTotal + $invoiceTaxTotal);

        $invoiceArray = [
            'id'        => $invoiceRow->id,
            'number'    => $invoiceRow->invoice_number ?? ('INV-' . $invoiceRow->id),
            'issue_date'=> date('Y-m-d', strtotime($invoiceRow->created_at ?? now())),
            'currency'  => $supplier['currency'] ?? 'SAR',
            'supplier'  => $supplier,
            'customer'  => $customer,
            'lines'     => $lines,
            'line_extension_total' => $lineExtensionTotal,
            'tax_total'            => (float) $invoiceTaxTotal,
            'tax_percent'          => 15,
            'legal_monetary_total' => [
                'payableAmount' => (float) $payable,
            ],
        ];

        Log::info('Invoice array built successfully', array_merge($this->logContext, [
            'invoice_id' => $invoiceId,
            'line_count' => count($lines),
            'total_amount' => $payable
        ]));

        return $invoiceArray;
    }

    /**
     * Get supplier information from database
     */
    protected function getSupplierInformation(object $invoiceRow): array
    {
        $branch = null;
        if (!empty($invoiceRow->branch)) {
            $branch = DB::table('branches')->where('id', $invoiceRow->branch)->first();
        }

        $admin = DB::table('adminusers')->first();

        return [
            'name'       => $branch->company ?? $admin->name ?? config('app.name'),
            'vat_number' => $branch->vat_number ?? $admin->vat_number ?? $admin->trn_number ?? null,
            'address'    => $branch->address ?? $admin->address ?? ($invoiceRow->from_address ?? null),
            'city'       => $branch->city ?? $admin->city ?? 'Riyadh',
            'country'    => $branch->country ?? $admin->country ?? 'SA',
            'currency'   => $branch->currency ?? 'SAR',
        ];
    }

    /**
     * Get customer information from database
     */
    protected function getCustomerInformation(object $invoiceRow): array
    {
        return [
            'name'       => $invoiceRow->to_name ?? '',
            'vat_number' => $invoiceRow->to_trnnumber ?? ($invoiceRow->to_number ?? ''),
            'address'    => $invoiceRow->to_address ?? '',
        ];
    }

    /**************************************************************************
     * --------------------------- XSD Validation ----------------------------
     **************************************************************************/

    public function validateUbl(string $xmlString, string $mainXsdPath): array
    {
        Log::info('Validating UBL against XSD', array_merge($this->logContext, [
            'xsd_path' => $mainXsdPath
        ]));

        if (!file_exists($mainXsdPath)) {
            $error = 'XSD not found: ' . $mainXsdPath;
            Log::error($error, $this->logContext);
            return ['valid' => false, 'errors' => [$error]];
        }

        libxml_use_internal_errors(true);
        $doc = new DOMDocument();
        $doc->preserveWhiteSpace = false;
        $doc->formatOutput = false;

        if (!$doc->loadXML($xmlString)) {
            $errors = libxml_get_errors();
            libxml_clear_errors();
            Log::error('Failed to load XML for validation', array_merge($this->logContext, [
                'errors' => array_map(fn($e) => trim($e->message), $errors)
            ]));
            return ['valid' => false, 'errors' => array_map(fn($e) => trim($e->message), $errors)];
        }

        $valid = @$doc->schemaValidate($mainXsdPath);
        $errors = libxml_get_errors();
        libxml_clear_errors();

        $result = [
            'valid' => (bool)$valid,
            'errors' => array_map(fn($e) => trim($e->message), $errors)
        ];

        if ($valid) {
            Log::info('XSD validation successful', $this->logContext);
        } else {
            Log::error('XSD validation failed', array_merge($this->logContext, [
                'errors' => $result['errors']
            ]));
        }

        return $result;
    }

    public function validateAgainstZatcaXsd(string $xmlString): array
    {
        $zatcaXsdPath = storage_path('xsd/UBL-Invoice-2.1.xsd');
        Log::info('Validating against ZATCA XSD', array_merge($this->logContext, [
            'xsd_path' => $zatcaXsdPath
        ]));
        
        return $this->validateUbl($xmlString, $zatcaXsdPath);
    }

    /**************************************************************************
     * --------------------------- Signing (XAdES-BES) -----------------------
     **************************************************************************/

    public function signXML(string $xmlString, string $referenceIdAttrName = 'Id'): string
    {
        Log::info('Starting XML signing process', $this->logContext);
        
        $doc = $this->loadAndPrepareXML($xmlString, $referenceIdAttrName);
        $root = $doc->documentElement;

        $rootId = $root->getAttribute($referenceIdAttrName);
        if (!$rootId) {
            $rootId = 'invoice-' . bin2hex(random_bytes(4));
            $root->setAttribute($referenceIdAttrName, $rootId);
            Log::debug("Added missing ID attribute to root: {$rootId}", $this->logContext);
        }

        $xmlSecurity = new XMLSecurityDSig();
        $xmlSecurity->setCanonicalMethod(XMLSecurityDSig::EXC_C14N);
        Log::debug('Using EXC_C14N canonicalization method', $this->logContext);

        // Reference the root
        $xmlSecurity->addReference(
            $root,
            XMLSecurityDSig::SHA256,
            ['http://www.w3.org/2000/09/xmldsig#enveloped-signature'],
            ['force_uri' => true, 'uri' => '#' . $rootId]
        );

        $signatureId    = 'SIG-' . bin2hex(random_bytes(4));
        $signedPropsId  = 'SignedProps-' . bin2hex(random_bytes(4));

        Log::debug("Signature IDs generated", array_merge($this->logContext, [
            'signature_id' => $signatureId,
            'signed_props_id' => $signedPropsId
        ]));

        // Create XAdES elements
        $xadesElements = $this->createXAdESElements($doc, $signatureId, $signedPropsId);

        // Add reference for SignedProperties
        $xmlSecurity->addReference(
            $xadesElements['signedProperties'],
            XMLSecurityDSig::SHA256,
            [XMLSecurityDSig::EXC_C14N],
            [
                'force_uri' => true,
                'uri'       => '#' . $signedPropsId,
                'type'      => 'http://uri.etsi.org/01903#SignedProperties'
            ]
        );

        // Sign with private key
        $privateKey = new XMLSecurityKey(XMLSecurityKey::RSA_SHA256, ['type' => 'private']);
        $privateKey->loadKey($this->privateKey, false);

        $xmlSecurity->sign($privateKey);
        $xmlSecurity->add509Cert($this->certPem, true, false, ['subjectName' => true]);

        $signatureNode = $xmlSecurity->sigNode;
        $signatureNode->setAttribute('Id', $signatureId);

        // Import the QualifyingProperties
        $qualifyingProps = $doc->importNode($xadesElements['qualifyingProperties'], true);
        $signatureNode->appendChild($qualifyingProps);

        // Import and append the Signature node
        $root->appendChild($doc->importNode($signatureNode, true));

        $signedXml = $doc->saveXML();
        Log::info('XML signed successfully', array_merge($this->logContext, [
            'signature_id' => $signatureId,
            'signed_xml_length' => strlen($signedXml)
        ]));

        return $signedXml;
    }

    protected function loadAndPrepareXML(string $xmlString, string $referenceIdAttrName): DOMDocument
    {
        Log::debug('Loading and preparing XML for signing', $this->logContext);
        
        $doc = new DOMDocument('1.0', 'UTF-8');
        $doc->preserveWhiteSpace = false;
        $doc->formatOutput       = false;

        $loaded = $doc->loadXML(
            $xmlString,
            LIBXML_NOBLANKS | LIBXML_NOCDATA | LIBXML_NONET | LIBXML_NOERROR | LIBXML_NOWARNING
        );

        if (!$loaded) {
            $error = 'Invalid XML provided for signing';
            Log::error($error, $this->logContext);
            throw new RuntimeException($error);
        }

        $root = $doc->documentElement;
        if (!$root) {
            $error = 'No root element in XML';
            Log::error($error, $this->logContext);
            throw new RuntimeException($error);
        }

        if (!$root->hasAttribute($referenceIdAttrName)) {
            $rootId = 'root-' . bin2hex(random_bytes(8));
            $root->setAttribute($referenceIdAttrName, $rootId);
            Log::debug("Added missing ID attribute to root: {$rootId}", $this->logContext);
        }

        return $doc;
    }

    protected function createXAdESElements(DOMDocument $doc, string $signatureId, string $signedPropsId): array
    {
        Log::debug('Creating XAdES elements', array_merge($this->logContext, [
            'signature_id' => $signatureId,
            'signed_props_id' => $signedPropsId
        ]));

        // QualifyingProperties
        $qualifyingProperties = $doc->createElementNS(self::NS_XADES, 'xades:QualifyingProperties');
        $qualifyingProperties->setAttribute('Target', '#' . $signatureId);

        // SignedProperties
        $signedProperties = $doc->createElementNS(self::NS_XADES, 'xades:SignedProperties');
        $signedProperties->setAttribute('Id', $signedPropsId);

        // SignedSignatureProperties
        $signedSignatureProperties = $doc->createElementNS(self::NS_XADES, 'xades:SignedSignatureProperties');

        // SigningTime
        $signingTime = $doc->createElementNS(self::NS_XADES, 'xades:SigningTime', gmdate('Y-m-d\TH:i:s\Z'));
        $signedSignatureProperties->appendChild($signingTime);

        // SigningCertificate
        $signingCertificate = $this->createSigningCertificateElement($doc);
        if ($signingCertificate) {
            $signedSignatureProperties->appendChild($signingCertificate);
        }

        // SignaturePolicyIdentifier (ZATCA required)
        $signaturePolicyIdentifier = $doc->createElementNS(self::NS_XADES, 'xades:SignaturePolicyIdentifier');
        $signaturePolicyId = $doc->createElementNS(self::NS_XADES, 'xades:SignaturePolicyId');
        
        $sigPolicyId = $doc->createElementNS(self::NS_XADES, 'xades:SigPolicyId');
        $identifier = $doc->createElementNS(self::NS_XADES, 'xades:Identifier', 'urn:oid:1.2.3.4.5');
        $identifier->setAttribute('Qualifier', 'OIDAsURI');
        $sigPolicyId->appendChild($identifier);
        
        $sigPolicyHash = $doc->createElementNS(self::NS_XADES, 'xades:SigPolicyHash');
        $digestMethod = $doc->createElementNS(self::NS_DS, 'ds:DigestMethod');
        $digestMethod->setAttribute('Algorithm', 'http://www.w3.org/2001/04/xmlenc#sha256');
        $digestValue = $doc->createElementNS(self::NS_DS, 'ds:DigestValue', 'base64_encoded_hash_here');
        $sigPolicyHash->appendChild($digestMethod);
        $sigPolicyHash->appendChild($digestValue);
        
        $signaturePolicyId->appendChild($sigPolicyId);
        $signaturePolicyId->appendChild($sigPolicyHash);
        $signaturePolicyIdentifier->appendChild($signaturePolicyId);
        $signedSignatureProperties->appendChild($signaturePolicyIdentifier);

        // Add to structure
        $signedProperties->appendChild($signedSignatureProperties);
        $qualifyingProperties->appendChild($signedProperties);

        Log::debug('XAdES elements created successfully', $this->logContext);
        
        return [
            'qualifyingProperties' => $qualifyingProperties,
            'signedProperties' => $signedProperties,
        ];
    }

    protected function createSigningCertificateElement(DOMDocument $doc): ?DOMElement
    {
        Log::debug('Creating signing certificate element', $this->logContext);
        
        $certInfo = openssl_x509_parse($this->certPem);
        if (!$certInfo) {
            Log::warning('Failed to parse certificate for signing certificate element', $this->logContext);
            return null;
        }

        $signingCertificate = $doc->createElementNS(self::NS_XADES, 'xades:SigningCertificate');
        $cert = $doc->createElementNS(self::NS_XADES, 'xades:Cert');

        // CertDigest
        $certDigest = $doc->createElementNS(self::NS_XADES, 'xades:CertDigest');
        $digestMethod = $doc->createElementNS(self::NS_DS, 'ds:DigestMethod');
        $digestMethod->setAttribute('Algorithm', 'http://www.w3.org/2001/04/xmlenc#sha256');
        $digestValue = $doc->createElementNS(self::NS_DS, 'ds:DigestValue', base64_encode(openssl_x509_fingerprint($this->certPem, 'sha256', true)));
        $certDigest->appendChild($digestMethod);
        $certDigest->appendChild($digestValue);

        // IssuerSerial
        $issuerSerial = $doc->createElementNS(self::NS_XADES, 'xades:IssuerSerial');
        $x509IssuerName = $doc->createElementNS(self::NS_DS, 'ds:X509IssuerName', $this->issuerDn);
        $x509SerialNumber = $doc->createElementNS(self::NS_DS, 'ds:X509SerialNumber', $this->serialHex);
        $issuerSerial->appendChild($x509IssuerName);
        $issuerSerial->appendChild($x509SerialNumber);

        $cert->appendChild($certDigest);
        $cert->appendChild($issuerSerial);
        $signingCertificate->appendChild($cert);

        Log::debug('Signing certificate element created', $this->logContext);
        return $signingCertificate;
    }

    /**************************************************************************
     * --------------------------- QR Code Generation ------------------------
     **************************************************************************/

    public function generateQRCode(array $invoiceData, array $sellerInfo): string
    {
        Log::info('Generating QR code', array_merge($this->logContext, [
            'invoice_id' => $invoiceData['id'] ?? 'unknown'
        ]));

        $tlvData = $this->generateTLVData($invoiceData, $sellerInfo);
        $base64QR = $this->generateBase64QRCode($tlvData);

        Log::info('QR code generated successfully', array_merge($this->logContext, [
            'invoice_id' => $invoiceData['id'] ?? 'unknown',
            'qr_data_length' => strlen($tlvData)
        ]));

        return $base64QR;
    }

    protected function generateTLVData(array $invoiceData, array $sellerInfo): string
    {
        $sellerName = $sellerInfo['name'] ?? '';
        $vatNumber = $sellerInfo['vat_number'] ?? '';
        $invoiceTotal = number_format((float)($invoiceData['legal_monetary_total']['payableAmount'] ?? 0), 2, '.', '');
        $vatTotal = number_format((float)($invoiceData['tax_total'] ?? 0), 2, '.', '');
        $timestamp = date('Y-m-d\TH:i:s\Z', strtotime($invoiceData['issue_date'] ?? 'now'));

        $tlv = '';
        $tlv .= $this->createTLV(1, $sellerName);         // Seller name
        $tlv .= $this->createTLV(2, $vatNumber);          // VAT number
        $tlv .= $this->createTLV(3, $timestamp);          // Invoice timestamp
        $tlv .= $this->createTLV(4, $invoiceTotal);       // Invoice total
        $tlv .= $this->createTLV(5, $vatTotal);           // VAT total

        Log::debug('TLV data generated', array_merge($this->logContext, [
            'seller_name' => $sellerName,
            'vat_number' => $vatNumber,
            'timestamp' => $timestamp,
            'invoice_total' => $invoiceTotal,
            'vat_total' => $vatTotal
        ]));

        return $tlv;
    }

    protected function createTLV(int $tag, string $value): string
    {
        $value = trim($value);
        $length = strlen($value);
        
        return chr($tag) . chr($length) . $value;
    }

    protected function generateBase64QRCode(string $tlvData): string
    {
        try {
            $qrCode = QrCode::create($tlvData)
                ->setEncoding(new Encoding('UTF-8'))
                ->setErrorCorrectionLevel(ErrorCorrectionLevel::High)
                ->setSize(300)
                ->setMargin(10);
            
            $writer = new PngWriter();
            $result = $writer->write($qrCode);
            
            $qrCodeImage = $result->getString();
            $base64Image = base64_encode($qrCodeImage);
            
            Log::debug('QR code image generated', array_merge($this->logContext, [
                'image_size' => strlen($qrCodeImage),
                'base64_size' => strlen($base64Image)
            ]));
            
            return $base64Image;
        } catch (\Exception $e) {
            Log::error('Failed to generate QR code', array_merge($this->logContext, [
                'error' => $e->getMessage()
            ]));
            
            return '';
        }
    }

    /**************************************************************************
     * --------------------------- ZATCA Compliance --------------------------
     **************************************************************************/

    public function generateComplianceInvoice(int $invoiceId): array
    {
        Log::info('Generating compliance invoice', array_merge($this->logContext, [
            'invoice_id' => $invoiceId
        ]));

        // Validation: Reject invalid invoiceId values (0 or negative)
        if ($invoiceId <= 0) {
            $errorMsg = 'Invalid invoice ID provided.';
            Log::error($errorMsg, $this->logContext);
            return [
                'success' => false,
                'errors' => [$errorMsg],
                'invoice_id' => $invoiceId,
            ];
        }

        try {
            $invoiceArray = $this->buildInvoiceArray($invoiceId);
        } catch (\RuntimeException $ex) {
            // Capture the invoice not found exception and return gracefully
            Log::error('Invoice not found: ' . $ex->getMessage(), $this->logContext);
            return [
                'success' => false,
                'errors' => [$ex->getMessage()],
                'invoice_id' => $invoiceId,
            ];
        }

        $ublXml = $this->generateUblInvoice($invoiceArray);

        // Validate the XML
        $validation = $this->validateAgainstZatcaXsd($ublXml);
        if (!$validation['valid']) {
            Log::error('UBL validation failed', array_merge($this->logContext, [
                'invoice_id' => $invoiceId,
                'errors' => $validation['errors']
            ]));

            return [
                'success' => false,
                'errors' => $validation['errors'],
                'invoice_id' => $invoiceId,
            ];
        }

        // Sign the XML
        try {
            $signedXml = $this->signXML($ublXml);
        } catch (\Exception $e) {
            Log::error('XML signing failed', array_merge($this->logContext, [
                'invoice_id' => $invoiceId,
                'error' => $e->getMessage()
            ]));

            return [
                'success' => false,
                'errors' => ['XML signing failed: ' . $e->getMessage()],
                'invoice_id' => $invoiceId,
            ];
        }

        // Generate QR code
        $sellerInfo = $this->getSupplierInformation((object)$invoiceArray);
        $qrCode = $this->generateQRCode($invoiceArray, $sellerInfo);

        Log::info('Compliance invoice generated successfully', array_merge($this->logContext, [
            'invoice_id' => $invoiceId,
            'signed_xml_length' => strlen($signedXml),
            'has_qr_code' => !empty($qrCode)
        ]));

        return [
            'success' => true,
            'invoice_id' => $invoiceId,
            'ubl_xml' => $ublXml,
            'signed_xml' => $signedXml,
            'qr_code' => $qrCode,
            'validation' => $validation,
        ];
    }

    public function submitToZatca(int $invoiceId): array
    {
        Log::info('Submitting invoice to ZATCA', array_merge($this->logContext, [
            'invoice_id' => $invoiceId
        ]));

        $complianceInvoice = $this->generateComplianceInvoice($invoiceId);
        if (!$complianceInvoice['success']) {
            return $complianceInvoice;
        }

        try {
            $response = Http::withHeaders([
                'Accept' => 'application/json',
                'Content-Type' => 'application/xml',
                'OTP' => config('zatca.otp', ''),
            ])->withOptions([
                'verify' => config('zatca.verify_ssl', true),
                'timeout' => 30,
            ])->post($this->sandboxUrl . '/compliance', [
                'invoice' => $complianceInvoice['signed_xml']
            ]);

            $responseData = $response->json();
            $statusCode = $response->status();

            Log::info('ZATCA API response', array_merge($this->logContext, [
                'invoice_id' => $invoiceId,
                'status_code' => $statusCode,
                'response' => $responseData
            ]));

            if ($statusCode >= 200 && $statusCode < 300) {
                $this->recordSuccessfulSubmission($invoiceId, $responseData);
                
                return [
                    'success' => true,
                    'invoice_id' => $invoiceId,
                    'zatca_response' => $responseData,
                    'compliance_request_id' => $responseData['requestID'] ?? null,
                    'clearance_status' => $responseData['status'] ?? 'unknown'
                ];
            } else {
                $this->recordFailedSubmission($invoiceId, $statusCode, $responseData);
                
                return [
                    'success' => false,
                    'invoice_id' => $invoiceId,
                    'zatca_response' => $responseData,
                    'status_code' => $statusCode,
                    'errors' => [$responseData['error'] ?? 'Unknown ZATCA error']
                ];
            }
        } catch (\Exception $e) {
            Log::error('ZATCA submission failed', array_merge($this->logContext, [
                'invoice_id' => $invoiceId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]));
            
            $this->recordFailedSubmission($invoiceId, 0, ['error' => $e->getMessage()]);
            
            return [
                'success' => false,
                'invoice_id' => $invoiceId,
                'errors' => ['ZATCA submission failed: ' . $e->getMessage()]
            ];
        }
    }

    protected function recordSuccessfulSubmission(int $invoiceId, array $responseData): void
    {
        Log::info('Recording successful ZATCA submission', array_merge($this->logContext, [
            'invoice_id' => $invoiceId
        ]));

        DB::table('invoicedatas')->where('id', $invoiceId)->update([
            'zatca_status' => 'submitted',
            'zatca_request_id' => $responseData['requestID'] ?? null,
            'zatca_response' => json_encode($responseData),
            'zatca_submitted_at' => now(),
            'updated_at' => now(),
        ]);
    }

    protected function recordFailedSubmission(int $invoiceId, int $statusCode, array $responseData): void
    {
        Log::warning('Recording failed ZATCA submission', array_merge($this->logContext, [
            'invoice_id' => $invoiceId,
            'status_code' => $statusCode
        ]));

        DB::table('invoicedatas')->where('id', $invoiceId)->update([
            'zatca_status' => 'failed',
            'zatca_response' => json_encode($responseData),
            'zatca_submitted_at' => now(),
            'updated_at' => now(),
        ]);
    }

    /**************************************************************************
     * --------------------------- Utility Methods ---------------------------
     **************************************************************************/

    protected function convertPemToDer(string $pem): string
    {
        $begin = '-----BEGIN CERTIFICATE-----';
        $end = '-----END CERTIFICATE-----';
        
        $pem = trim($pem);
        if (strpos($pem, $begin) === 0) {
            $pem = substr($pem, strlen($begin));
        }
        if (($pos = strpos($pem, $end)) !== false) {
            $pem = substr($pem, 0, $pos);
        }
        
        $der = base64_decode(trim($pem));
        if ($der === false) {
            throw new RuntimeException('Failed to convert PEM to DER');
        }
        
        return $der;
    }

    protected function formatDistinguishedName(array $dnParts): string
    {
        $parts = [];
        foreach ($dnParts as $key => $value) {
            $parts[] = strtoupper($key) . '=' . $value;
        }
        return implode(',', $parts);
    }

    protected function convertSerialToHex($serial, $hexSerial): string
    {
        if ($hexSerial) {
            return strtoupper($hexSerial);
        }
        
        if (is_string($serial)) {
            $serial = trim($serial);
            if (preg_match('/^[0-9a-fA-F]+$/', $serial)) {
                return strtoupper($serial);
            }
            
            $hex = '';
            for ($i = 0; $i < strlen($serial); $i++) {
                $hex .= dechex(ord($serial[$i]));
            }
            return strtoupper($hex);
        }
        
        if (is_numeric($serial)) {
            return dechex((int)$serial);
        }
        
        return bin2hex((string)$serial);
    }

    /**
     * Clean up resources
     */
    public function __destruct()
    {
        if ($this->privateKey !== null) {
            $this->privateKey = null;
            Log::debug('Private key reference cleared', $this->logContext);
        }
    }
}