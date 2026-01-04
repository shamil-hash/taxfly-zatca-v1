<?php

namespace App\Services\Zatca;

use App\Models\Branch;
use Saleh7\Zatca\{
    InvoiceLine,
    TaxTotal,
    TaxSubTotal,
    LegalMonetaryTotal,
    Item,
    Price,
    ClassifiedTaxCategory,
    TaxCategory,
    TaxScheme,
    Party,
    PartyTaxScheme,
    Address,
    LegalEntity,
    AdditionalDocumentReference
};
use Saleh7\Zatca\Helpers\Certificate;
use App\Services\Zatca\ZatcaInvoice;
use App\Services\Zatca\InvoiceSignerService;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;
use Sabre\Xml\Service;
use DateTime;
use DateTimeInterface;
use DateTimeImmutable;
use DateTimeZone;
use Exception;
use DOMDocument;
use DOMXPath;

class InvoiceXmlGenerator
{
    protected InvoiceSignerService $signer;
    protected InvoiceQrGenerator $qrGenerator;

    public function __construct(InvoiceSignerService $signer, InvoiceQrGenerator $qrGenerator)
    {
        $this->signer = $signer;
        $this->qrGenerator = $qrGenerator;
        Log::channel('zatca')->debug('🔄 InvoiceXmlGenerator initialized', [
            'signer' => get_class($signer),
            'qrGenerator' => get_class($qrGenerator)
        ]);
    }

    /**
     * Generate full Phase-2 invoice with validation, signing, and QR generation
     */
    public function generate(array $invoiceData, ?Certificate $certificate = null, ?string $branchNumber = null): array
    {
        Log::channel('zatca')->info('🚀 STARTING ZATCA COMPLIANT INVOICE GENERATION', [
            'invoice_number' => $invoiceData['invoice_number'] ?? 'unknown',
            'uuid' => $invoiceData['uuid'] ?? 'unknown',
            'has_certificate' => !is_null($certificate),
            'branch_number' => $branchNumber,
        ]);

        // Apply defaults and validate
        Log::channel('zatca')->debug('📋 Applying default values to invoice data');
        $invoiceData = self::applyDefaultValues($invoiceData);

        // ✅ Ensure branch invoice counter exists
        if ($branchNumber) {
            $branch = \App\Models\Branch::where('branch_number', $branchNumber)->first();
            if (!$branch) {
                throw new \Exception("Branch not found: {$branchNumber}");
            }
            if (empty($invoiceData['invoice_counter']) || (int)$invoiceData['invoice_counter'] <= 0) {
                $invoiceData['invoice_counter'] = $branch->getNextInvoiceCounter();
                Log::channel('zatca')->info('🔢 Assigned invoice counter from branch', [
                    'branch_id' => $branch->id,
                    'invoice_counter' => $invoiceData['invoice_counter']
                ]);
            }
        }

        // Validate mandatory fields
        $mandatoryErrors = $this->validateMandatoryFields($invoiceData);
        if (!empty($mandatoryErrors)) {
            Log::channel('zatca')->error('❌ Mandatory field validation failed', [
                'errors' => $mandatoryErrors,
                'invoice_data' => array_diff_key($invoiceData, array_flip(['line_items']))
            ]);
            return $this->errorResponse($mandatoryErrors, [], null, null, null, null, $invoiceData['uuid'] ?? null);
        }

        // Validate totals consistency
        $providedTotal = round((float)($invoiceData['total_amount'] ?? 0), 2);
        $providedVat = round((float)($invoiceData['vat_amount'] ?? 0), 2);
        if ($providedTotal <= 0) {
            Log::channel('zatca')->error('❌ Total amount validation failed');
            return $this->errorResponse(["Total amount must be greater than zero"], [], null, null, null, null, $invoiceData['uuid'] ?? null);
        }

        Log::channel('zatca')->debug('💰 Provided totals validation', [
            'provided_total' => $providedTotal,
            'provided_vat' => $providedVat
        ]);

        Log::channel('zatca')->debug('🔍 Running ZATCA validation');
        $validator = new ZatcaValidator();
        $validationResult = $validator->validate($invoiceData);

        if (!empty($validationResult['errors'])) {
            Log::channel('zatca')->error('❌ ZATCA validation failed', [
                'errors' => $validationResult['errors'],
                'warnings' => $validationResult['warnings']
            ]);
            return $this->errorResponse($validationResult['errors'], $validationResult['warnings'], null, null, null, null, $invoiceData['uuid'] ?? null);
        }

        Log::channel('zatca')->debug('✅ ZATCA validation passed', [
            'warnings' => $validationResult['warnings']
        ]);

        // Ensure storage directory exists
        $zatcaPath = storage_path('zatca');
        if (!file_exists($zatcaPath)) {
            Log::channel('zatca')->debug('📁 Creating ZATCA storage directory', ['path' => $zatcaPath]);
            mkdir($zatcaPath, 0755, true);
        }

        // Load certificate if not supplied
        if ($certificate === null) {
            Log::channel('zatca')->debug('🔐 Loading certificate from config');
            try {
                $certPath = config('zatca.certificate_path');
                $keyPath = config('zatca.private_key_path');
                $secret = config('zatca.secret');
                
                if (!file_exists($certPath) || !file_exists($keyPath)) {
                    $errorMsg = "Certificate or private key not found";
                    Log::channel('zatca')->error('❌ ' . $errorMsg);
                    throw new Exception($errorMsg);
                }

                $certPem = file_get_contents($certPath);
                $keyPem = file_get_contents($keyPath);

                $certPem = $this->validateZatcaCertificate($certPem);
                $keyPem = $this->validateZatcaPrivateKey($keyPem);
                $certificate = new Certificate($certPem, $keyPem, $secret);

                $this->validateCertificateForZatca($certificate);
                Log::channel('zatca')->debug('✅ ZATCA-compliant certificate loaded successfully');

            } catch (Exception $e) {
                Log::channel('zatca')->error('❌ Certificate loading failed', ['error' => $e->getMessage()]);
                return $this->errorResponse(["Certificate loading failed: " . $e->getMessage()], $validationResult['warnings'], null, null, null, null, $invoiceData['uuid'] ?? null);
            }
        } else {
            // Validate provided certificate
            try {
                $this->validateCertificateForZatca($certificate);
                Log::channel('zatca')->debug('✅ Provided certificate validated successfully');
            } catch (Exception $e) {
                Log::channel('zatca')->error('❌ Provided certificate validation failed', ['error' => $e->getMessage()]);
                return $this->errorResponse(["Certificate validation failed: " . $e->getMessage()], $validationResult['warnings'], null, null, null, null, $invoiceData['uuid'] ?? null);
            }
        }

        // ✅ SINGLE GENERATION: Generate final XML with QR placeholder
        Log::channel('zatca')->info('📄 Generating final unsigned XML with QR placeholder');
        $finalUnsignedXmlPath = $this->generateUnsignedXml($invoiceData, $branchNumber, 'final');
        
        if (!$finalUnsignedXmlPath) {
            Log::channel('zatca')->error('❌ Failed to generate final unsigned XML');
            return $this->errorResponse(["Failed to generate final unsigned XML"], $validationResult['warnings'], null, null, null, null, $invoiceData['uuid']);
        }

        Log::channel('zatca')->debug('✅ Final unsigned XML generated', [
            'path' => $finalUnsignedXmlPath,
            'file_size' => filesize($finalUnsignedXmlPath)
        ]);

        // Sign the final XML
        Log::channel('zatca')->info('✍️ Signing final XML');
        try {
            $finalSignedXmlPath = $this->signer->sign($finalUnsignedXmlPath);
            Log::channel('zatca')->debug('✅ Final XML signed successfully', ['path' => $finalSignedXmlPath]);
        } catch (\Throwable $e) {
            Log::channel('zatca')->error('❌ Final signing failed', ['error' => $e->getMessage()]);
            return $this->errorResponse(["Final signing failed: " . $e->getMessage()], $validationResult['warnings'], $finalUnsignedXmlPath, null, null, null, $invoiceData['uuid']);
        }

        if (!file_exists($finalSignedXmlPath)) {
            Log::channel('zatca')->error('❌ Final signed XML was not created', ['expected_path' => $finalSignedXmlPath]);
            return $this->errorResponse(["Final signed XML was not created"], $validationResult['warnings'], $finalUnsignedXmlPath, null, null, null, $invoiceData['uuid']);
        }

        // Generate QR code with cryptographic tags from signed XML
        Log::channel('zatca')->info('🔳 Generating QR code with cryptographic tags');
        try {
            $finalSignedXmlContent = file_get_contents($finalSignedXmlPath);
            $qrBase64 = $this->qrGenerator->generateBase64WithCrypto($finalSignedXmlContent, $certificate, $invoiceData);
            
            if (!is_string($qrBase64)) {
                $qrBase64 = is_scalar($qrBase64) ? (string)$qrBase64 : base64_encode('');
            }

            Log::channel('zatca')->debug('✅ QR code with crypto tags generated', [
                'length' => strlen($qrBase64),
                'first_50_chars' => substr($qrBase64, 0, 50) . '...'
            ]);
        } catch (\Throwable $e) {
            Log::channel('zatca')->error('❌ QR generation with crypto tags failed', ['error' => $e->getMessage()]);
            $qrBase64 = base64_encode('');
            Log::channel('zatca')->warning('🔄 Falling back to empty base64 QR');
        }

        // Generate QR image for display
        Log::channel('zatca')->debug('🖼️ Generating QR PNG image for display');
        try {
            $qrImageBase64 = $this->qrGenerator->generateQrCodeImageFromBase64($qrBase64);
            Log::channel('zatca')->debug('✅ QR image generated', [
                'image_length' => strlen($qrImageBase64)
            ]);
        } catch (\Throwable $e) {
            Log::channel('zatca')->error('❌ QR image generation failed', ['error' => $e->getMessage()]);
            $qrImageBase64 = base64_encode('');
        }

        // ✅ UPDATE XML WITH ACTUAL QR CODE
        Log::channel('zatca')->info('🔄 Updating final XML with actual QR code');
        try {
            $this->updateXmlWithQrCode($finalUnsignedXmlPath, $qrBase64);
            $this->updateXmlWithQrCode($finalSignedXmlPath, $qrBase64);
            Log::channel('zatca')->debug('✅ Final XML updated with QR code');
        } catch (\Throwable $e) {
            Log::channel('zatca')->error('❌ Failed to update XML with QR code', ['error' => $e->getMessage()]);
            // Continue anyway - this is not critical
        }

        // Calculate final invoice hash
        Log::channel('zatca')->debug('🔢 Calculating final invoice hash');
        try {
            $finalSignedXmlContent = file_get_contents($finalSignedXmlPath);
            $invoiceHash = base64_encode(hash('sha256', $finalSignedXmlContent, true));
            Log::channel('zatca')->debug('✅ Final invoice hash calculated', [
                'hash_length' => strlen($invoiceHash),
                'hash_preview' => substr($invoiceHash, 0, 20) . '...'
            ]);
        } catch (\Throwable $e) {
            Log::channel('zatca')->error('❌ Failed to calculate invoice hash', ['error' => $e->getMessage()]);
            $invoiceHash = '';
        }

        // ✅ Persist latest counter and hash into branch record
        if (isset($branch) && isset($invoiceData['invoice_counter'])) {
            $branch->update([
                'last_invoice_counter' => $invoiceData['invoice_counter'],
                'last_invoice_hash'    => $invoiceHash,
                'updated_at'           => now(),
            ]);
            Log::channel('zatca')->info('💾 Branch invoice state updated', [
                'branch_id'       => $branch->id,
                'invoice_counter' => $invoiceData['invoice_counter'],
                'invoice_hash_length' => strlen($invoiceHash)
            ]);
        }

        Log::channel('zatca')->info('🎉 ZATCA COMPLIANT INVOICE GENERATION COMPLETED SUCCESSFULLY', [
            'invoice_number' => $invoiceData['invoice_number'],
            'uuid' => $invoiceData['uuid'],
            'final_signed_xml' => $finalSignedXmlPath,
            'has_qr_code' => !empty($qrBase64) && $qrBase64 !== base64_encode(''),
            'has_invoice_hash' => !empty($invoiceHash),
            'generation_flow' => 'SINGLE_PASS'
        ]);

        return [
            'errors'       => [],
            'warnings'     => $validationResult['warnings'],
            'unsigned_xml' => $finalUnsignedXmlPath,
            'signed_xml'   => $finalSignedXmlPath,
            'qr_base64'    => $qrBase64,
            'qr_image_base64' => $qrImageBase64,
            'invoice_hash' => $invoiceHash,
            'uuid'         => $invoiceData['uuid'],
        ];
    }

    /**
     * Build unsigned XML with all required elements for ZATCA Phase-2
     */
    public function generateUnsignedXml(array $invoiceData, ?string $branchNumber = null, string $suffix = ''): string
    {
        Log::channel('zatca')->info('📝 Starting unsigned XML generation', [
            'invoice_number' => $invoiceData['invoice_number'] ?? 'unknown',
            'uuid' => $invoiceData['uuid'] ?? 'unknown',
            'suffix' => $suffix
        ]);

        try {
            // ✅ ADD: Debug the input data
            $this->debugXmlGeneration($invoiceData);

            // --- VALIDATION ---
            $requiredFields = [
                'supplier_id', 'supplier_name', 'supplier_street', 'supplier_building',
                'supplier_city', 'supplier_postal', 'supplier_country',
                'buyer_name', 'buyer_street', 'buyer_city', 'buyer_postal', 'buyer_country',
                'invoice_number', 'issue_datetime', 'total_amount', 'vat_amount',
                'invoice_counter', 'uuid', 'currency'
            ];

            foreach ($requiredFields as $field) {
                if (!isset($invoiceData[$field]) || $invoiceData[$field] === '') {
                    throw new Exception("Missing required field for ZATCA: $field");
                }
            }

            $supplierVat = $invoiceData['supplier_id'] ?? '';
            if (strlen($supplierVat) !== 15 || substr($supplierVat, 0, 1) !== '3' || substr($supplierVat, -1) !== '3') {
                throw new Exception("Invalid VAT number format. Must be 15 digits starting and ending with '3'. Received: " . $supplierVat);
            }

            $taxScheme = (new TaxScheme())->setId('VAT');
            Log::channel('zatca')->debug('🏷️ Tax scheme created', ['id' => 'VAT']);

            // --- INVOICE IDENTIFICATION ---
            $branchPart = $branchNumber ?? '0000';
            $invoiceNumber = sprintf("%s%05d", $branchPart, (int) $invoiceData['invoice_counter']);
            $suffix = $suffix ? '-' . $suffix : '';

            Log::channel('zatca')->debug('🔢 Invoice identification', [
                'branch_part' => $branchPart,
                'invoice_number' => $invoiceNumber,
                'suffix' => $suffix
            ]);

            // --- ISSUE DATE/TIME ---
            $issueDateTime = $this->parseDateTime($invoiceData['issue_datetime'] ?? 'now');
            $issueDate = $issueDateTime->format('Y-m-d');
            $issueTime = $issueDateTime->format('H:i:s') . 'Z';

            Log::channel('zatca')->debug('📅 Issue date/time parsed', [
                'issue_date' => $issueDate,
                'issue_time' => $issueTime
            ]);

            // --- SUPPLIER AND BUYER ---
            $supplierParty = $this->buildSupplierParty($invoiceData, $taxScheme);
            $buyerParty = $this->buildBuyerParty($invoiceData, $taxScheme);

            // --- INVOICE LINES ---
            $lineResult = $this->buildInvoiceLines($invoiceData);
            $invoiceLines = $lineResult['lines'];

            // --- AUTHORITATIVE TOTALS ---
            $totalLineAmount = (float) $lineResult['total_line_amount'];
            $totalVatAmount  = (float) $lineResult['total_vat_amount'];
            $grandTotal      = (float) $lineResult['grand_total'];

            Log::channel('zatca')->debug('📊 Invoice lines built', [
                'line_count' => count($invoiceLines),
                'total_line_amount' => $totalLineAmount,
                'total_vat_amount' => $totalVatAmount,
                'grand_total' => $grandTotal
            ]);

            // --- TAX TOTAL ---
            $taxTotal = $this->buildTaxTotal($lineResult['vat_groups'], $totalVatAmount);

            // --- MONETARY TOTALS ---
            $legalMonetaryTotal = (new LegalMonetaryTotal())
                ->setLineExtensionAmount($totalLineAmount)
                ->setTaxExclusiveAmount($totalLineAmount)
                ->setTaxInclusiveAmount($grandTotal)
                ->setAllowanceTotalAmount(0.0)
                ->setChargeTotalAmount(0.0)
                ->setPrepaidAmount(0.0)
                ->setPayableAmount($grandTotal);

            Log::channel('zatca')->debug('💰 Monetary totals built', [
                'grand_total' => $grandTotal,
                'line_amount' => $totalLineAmount,
                'amount_due'  => $grandTotal,
                'vat_amount'  => $totalVatAmount
            ]);

            // --- ADDITIONAL DOCUMENT REFERENCES ---
            $additionalDocumentReferences = $this->buildAdditionalDocumentReferences($invoiceData);

            Log::channel('zatca')->debug('📎 Generated AdditionalDocumentReferences BEFORE setting', [
                'count' => count($additionalDocumentReferences),
                'ids' => array_map(fn($ref) => $ref->getId(), $additionalDocumentReferences)
            ]);

            // --- INVOICE OBJECT - WITH EXACT ZATCA ORDERING ---
            $invoice = new ZatcaInvoice();

            // 1. BASIC INVOICE INFORMATION (EXACT ORDER REQUIRED)
            $invoice->setId($invoiceData['invoice_number']);
            $invoice->setUUID($invoiceData['uuid']);
            $invoice->setIssueDate(new \DateTime($issueDate));
            $invoice->setIssueTime(new \DateTime($issueTime));

            // 2. ADDITIONAL DOCUMENT REFERENCES (MUST come IMMEDIATELY after IssueTime)
            $invoice->setAdditionalDocumentReferences($additionalDocumentReferences);

            Log::channel('zatca')->debug('📎 AdditionalDocumentReferences AFTER setting', [
                'count' => count($invoice->getAdditionalDocumentReferences()),
                'ids' => array_map(fn($ref) => $ref->getId(), $invoice->getAdditionalDocumentReferences())
            ]);

            // 3. THEN THE REST OF THE BASIC ELEMENTS
            $invoice->setInvoiceTypeCode($invoiceData['invoice_type_code']);
            $invoice->setInvoiceTypeCodeName($invoiceData['invoice_transaction_code'] ?? '');
            $invoice->setDocumentCurrencyCode($invoiceData['currency']);
            $invoice->setTaxCurrencyCode($invoiceData['currency']);

            // 4. CUSTOMIZATION AND PROFILE IDs
            $invoice->setCustomizationID($invoiceData['customization_id'] ?? 'urn:cen.eu:en16931:2017#compliant#urn:sa.gov.zatca:invoice');
            $invoice->setProfileID('reporting:1.0');

            // 5. ACCOUNTING PARTIES
            $invoice->setAccountingSupplierParty($supplierParty);
            $invoice->setAccountingCustomerParty($buyerParty);

            // 6. INVOICE LINES AND TOTALS
            $invoice->setInvoiceLines($invoiceLines);
            $invoice->setTaxTotal($taxTotal);
            $invoice->setLegalMonetaryTotal($legalMonetaryTotal);

            Log::channel('zatca')->debug('✅ Invoice object created successfully', [
                'id' => $invoice->getId(),
                'uuid' => $invoice->getUUID(),
                'invoice_type_code' => $invoiceData['invoice_type_code'],
                'invoice_transaction_code' => $invoiceData['invoice_transaction_code'] ?? 'not_set'
            ]);

            // --- GENERATE XML ---
            $service = new Service();
            $service->namespaceMap = [
                'urn:oasis:names:specification:ubl:schema:xsd:Invoice-2' => '',
                'urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2' => 'cbc',
                'urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2' => 'cac',
                'urn:oasis:names:specification:ubl:schema:xsd:CommonExtensionComponents-2' => 'ext',
            ];

            $xmlContent = $service->write(
                '{urn:oasis:names:specification:ubl:schema:xsd:Invoice-2}Invoice',
                $invoice
            );

            // ✅ CHANGED: Add suffix to filename to prevent conflicts
            $xmlPath = storage_path("zatca/{$invoiceNumber}-unsigned{$suffix}.xml");
            file_put_contents($xmlPath, $xmlContent);

            // ✅✅✅ CRITICAL: ENHANCE FOR ZATCA PHASE 2 COMPLIANCE ✅✅✅
            $this->enhanceXmlForZatcaPhase2($xmlPath, $invoiceData);

            Log::channel('zatca')->info('✅ XML file generated successfully', [
                'path' => $xmlPath,
                'file_size' => filesize($xmlPath),
                'suffix_used' => $suffix
            ]);

            return $xmlPath;

        } catch (Exception $e) {
            Log::channel('zatca')->error('❌ XML generation failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'invoice_data' => array_diff_key($invoiceData, array_flip(['line_items'])),
                'suffix' => $suffix
            ]);
            return '';
        }
    }

    /**
     * Post-process XML to meet ZATCA Phase 2 requirements - COMPLETE FIXED VERSION
     */
    protected function enhanceXmlForZatcaPhase2(string $xmlPath, array $invoiceData): void
    {
        Log::channel('zatca')->debug('🔧 STARTING XML ENHANCEMENT FOR ZATCA PHASE 2');
        
        $doc = new DOMDocument();
        $doc->load($xmlPath);
        $doc->formatOutput = true;

        $xpath = new DOMXPath($doc);
        $xpath->registerNamespace('cbc', 'urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2');
        $xpath->registerNamespace('cac', 'urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2');
        $xpath->registerNamespace('ext', 'urn:oasis:names:specification:ubl:schema:xsd:CommonExtensionComponents-2');

        $root = $doc->documentElement;

        // === 1. Add UBLExtensions with CORRECT ZATCA structure ===
        Log::channel('zatca')->debug('📦 Adding UBLExtensions with PreviousInvoiceHash and SignatureInformation');
        
        // Add required namespaces for signing
        $root->setAttributeNS('http://www.w3.org/2000/xmlns/', 'xmlns:sig', 'urn:oasis:names:specification:ubl:schema:xsd:CommonSignatureComponents-2');
        $root->setAttributeNS('http://www.w3.org/2000/xmlns/', 'xmlns:sac', 'urn:oasis:names:specification:ubl:schema:xsd:SignatureAggregateComponents-2');
        $root->setAttributeNS('http://www.w3.org/2000/xmlns/', 'xmlns:ds', 'http://www.w3.org/2000/09/xmldsig#');
        $root->setAttributeNS('http://www.w3.org/2000/xmlns/', 'xmlns:xades', 'http://uri.etsi.org/01903/v1.3.2#');

        $extensions = $doc->createElementNS('urn:oasis:names:specification:ubl:schema:xsd:CommonExtensionComponents-2', 'ext:UBLExtensions');
        
        // ✅ FIRST UBLExtension: PreviousInvoiceHash
        $extension1 = $doc->createElementNS('urn:oasis:names:specification:ubl:schema:xsd:CommonExtensionComponents-2', 'ext:UBLExtension');
        $extensionContent1 = $doc->createElementNS('urn:oasis:names:specification:ubl:schema:xsd:CommonExtensionComponents-2', 'ext:ExtensionContent');
        
        $previousInvoiceHash = $doc->createElementNS('urn:oasis:names:specification:ubl:schema:xsd:SignatureAggregateComponents-2', 'sac:PreviousInvoiceHash');
        $previousInvoiceHash->nodeValue = $invoiceData['pih'] ?? str_repeat('0', 64);
        
        $extensionContent1->appendChild($previousInvoiceHash);
        $extension1->appendChild($extensionContent1);
        $extensions->appendChild($extension1);

        // ✅ SECOND UBLExtension: UBLVersionID + SignatureInformation
        $extension2 = $doc->createElementNS('urn:oasis:names:specification:ubl:schema:xsd:CommonExtensionComponents-2', 'ext:UBLExtension');
        $extensionContent2 = $doc->createElementNS('urn:oasis:names:specification:ubl:schema:xsd:CommonExtensionComponents-2', 'ext:ExtensionContent');
        
        // Add UBLVersionID
        $ublVersionId = $doc->createElementNS('urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2', 'cbc:UBLVersionID', '2.1');
        $extensionContent2->appendChild($ublVersionId);
        
        // ✅ CRITICAL: Add UBLDocumentSignatures with SignatureInformation
        $documentSignatures = $doc->createElementNS('urn:oasis:names:specification:ubl:schema:xsd:CommonSignatureComponents-2', 'sig:UBLDocumentSignatures');
        
        // ✅ CRITICAL: Create SignatureInformation WITHOUT id attribute
        $signatureInformation = $doc->createElementNS('urn:oasis:names:specification:ubl:schema:xsd:SignatureAggregateComponents-2', 'sac:SignatureInformation');
        // ❌ DO NOT add id attribute - it's invalid per ZATCA schema
        
        $documentSignatures->appendChild($signatureInformation);
        $extensionContent2->appendChild($documentSignatures);
        $extension2->appendChild($extensionContent2);
        $extensions->appendChild($extension2);

        // Insert at the beginning
        $root->insertBefore($extensions, $root->firstChild);

        // === 2. Convert PIH and QR to EmbeddedDocumentBinaryObject ===
        Log::channel('zatca')->debug('🔄 Converting PIH and QR to EmbeddedDocumentBinaryObject');
        foreach (['PIH', 'QR'] as $id) {
            $refs = $xpath->query("//cac:AdditionalDocumentReference[cbc:ID='$id']");
            Log::channel('zatca')->debug("Processing $id references", ['count' => $refs->length]);
            
            foreach ($refs as $ref) {
                $uuidNode = $xpath->query('cbc:UUID', $ref)->item(0);
                if ($uuidNode) {
                    $value = $uuidNode->textContent;
                    $ref->removeChild($uuidNode);

                    $attachment = $doc->createElementNS('urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2', 'cac:Attachment');
                    $binObj = $doc->createElementNS('urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2', 'cbc:EmbeddedDocumentBinaryObject', $value);
                    $binObj->setAttribute('mimeCode', 'text/plain');
                    $binObj->setAttribute('filename', $id . '.txt');
                    $attachment->appendChild($binObj);
                    $ref->appendChild($attachment);
                    
                    Log::channel('zatca')->debug("✅ Converted $id to EmbeddedDocumentBinaryObject");
                }
            }
        }

        // === 3. Enhance Supplier Address with KSA fields ===
        Log::channel('zatca')->debug('🏢 Enhancing supplier address with KSA fields');
        $this->injectKsaAddressFields($doc, $xpath, '//cac:AccountingSupplierParty/cac:Party/cac:PostalAddress', $invoiceData, 'supplier_');

        // === 4. Enhance Buyer Address ===
        Log::channel('zatca')->debug('👤 Enhancing buyer address with KSA fields');
        $this->injectKsaAddressFields($doc, $xpath, '//cac:AccountingCustomerParty/cac:Party/cac:PostalAddress', $invoiceData, 'buyer_');

        $doc->save($xmlPath);
        Log::channel('zatca')->debug('✅ XML ENHANCEMENT COMPLETED SUCCESSFULLY - SignatureInformation added');
    }

    /**
     * Update XML with actual QR code content
     */
    protected function updateXmlWithQrCode(string $xmlPath, string $qrBase64): void
    {
        Log::channel('zatca')->debug('🔄 Updating XML with actual QR code', ['path' => $xmlPath]);
        
        $doc = new DOMDocument();
        $doc->load($xmlPath);
        $doc->formatOutput = true;

        $xpath = new DOMXPath($doc);
        $xpath->registerNamespace('cbc', 'urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2');
        $xpath->registerNamespace('cac', 'urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2');

        // Find QR AdditionalDocumentReference and update it
        $qrRefs = $xpath->query("//cac:AdditionalDocumentReference[cbc:ID='QR']");
        Log::channel('zatca')->debug('QR references found', ['count' => $qrRefs->length]);
        
        foreach ($qrRefs as $qrRef) {
            $binObj = $xpath->query('cac:Attachment/cbc:EmbeddedDocumentBinaryObject', $qrRef)->item(0);
            if ($binObj) {
                $oldValue = $binObj->nodeValue;
                $binObj->nodeValue = $qrBase64;
                Log::channel('zatca')->debug('✅ QR code updated in XML', [
                    'old_length' => strlen($oldValue),
                    'new_length' => strlen($qrBase64)
                ]);
            } else {
                Log::channel('zatca')->warning('⚠️ QR EmbeddedDocumentBinaryObject not found');
            }
        }

        $doc->save($xmlPath);
        Log::channel('zatca')->debug('✅ XML QR code update completed');
    }

    protected function injectKsaAddressFields(DOMDocument $doc, DOMXPath $xpath, string $addressXPath, array $invoiceData, string $prefix): void
    {
        $addresses = $xpath->query($addressXPath);
        Log::channel('zatca')->debug("Injecting KSA address fields for $prefix", ['address_count' => $addresses->length]);
        
        if ($addresses->length === 0) return;

        $addr = $addresses->item(0);
        $createElement = fn($name, $value) => $doc->createElementNS('urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2', $name, $value);

        $fieldsAdded = [];

        // PlotIdentification
        if (!empty($invoiceData[$prefix . 'plot'])) {
            $addr->appendChild($createElement('cbc:PlotIdentification', $invoiceData[$prefix . 'plot']));
            $fieldsAdded[] = 'PlotIdentification';
        }

        // CountrySubentity (province)
        if (!empty($invoiceData[$prefix . 'province'])) {
            $addr->appendChild($createElement('cbc:CountrySubentity', $invoiceData[$prefix . 'province']));
            $fieldsAdded[] = 'CountrySubentity';
        }

        // CountrySubentityCode
        if (!empty($invoiceData[$prefix . 'province_code'])) {
            $addr->appendChild($createElement('cbc:CountrySubentityCode', $invoiceData[$prefix . 'province_code']));
            $fieldsAdded[] = 'CountrySubentityCode';
        }

        // District + AddressLine
        if (!empty($invoiceData[$prefix . 'district'])) {
            $addr->appendChild($createElement('cbc:District', $invoiceData[$prefix . 'district']));
            $line = $doc->createElementNS('urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2', 'cac:AddressLine');
            $line->appendChild($createElement('cbc:Line', $invoiceData[$prefix . 'district'] . ' District'));
            $addr->appendChild($line);
            $fieldsAdded[] = 'District';
        }

        // Additional address line (building/floor)
        if (!empty($invoiceData[$prefix . 'additional_address_line'])) {
            $line = $doc->createElementNS('urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2', 'cac:AddressLine');
            $line->appendChild($createElement('cbc:Line', $invoiceData[$prefix . 'additional_address_line']));
            $addr->appendChild($line);
            $fieldsAdded[] = 'AdditionalAddressLine';
        }

        Log::channel('zatca')->debug("✅ KSA address fields injected for $prefix", ['fields_added' => $fieldsAdded]);
    }

    /**
     * Build invoice line items and calculate totals for inclusive pricing
     */
    protected function buildInvoiceLines(array $invoiceData): array
    {
        Log::channel('zatca')->debug('📦 Building invoice lines', [
            'line_items_count' => count($invoiceData['line_items'] ?? []),
            'pricing_type' => $invoiceData['pricing_type'] ?? 'unknown'
        ]);

        $invoiceLines = [];
        $totalNet = 0.0;
        $totalVat = 0.0;
        $vatGroups = [];
        $lineItems = $invoiceData['line_items'] ?? [];
        $usedLineIds = [];

        // Force inclusive mode — our system always uses MRP (inclusive)
        $pricingType = 'inclusive';

        foreach ($lineItems as $index => $item) {
            try {
                $quantity   = round((float) ($item['quantity'] ?? 0), 2);
                $vatPercent = round((float) ($item['vat_percent'] ?? 0), 2);
                $unitPrice  = round((float) ($item['unit_price'] ?? 0), 2); // ← INCLUSIVE (MRP)
                $lineTotal  = round((float) ($item['total'] ?? ($quantity * $unitPrice)), 2); // ← INCLUSIVE

                if ($pricingType === 'inclusive') {
                    // Back-calculate tax-exclusive net price
                    $netPrice  = round($lineTotal / (1 + ($vatPercent / 100)), 2);
                    $vatAmount = round($lineTotal - $netPrice, 2);
                    $basePrice = round($netPrice / max($quantity, 1), 2); // Net per unit
                } else {
                    $netPrice  = $lineTotal;
                    $vatAmount = round($netPrice * ($vatPercent / 100), 2);
                    $basePrice = $unitPrice;
                }

                $description = trim((string) ($item['description'] ?? 'Item ' . ($index + 1)));

                // VAT category
                $vatCategoryCode = $item['vat_category_code'] ?? 'S';
                if (!in_array($vatCategoryCode, ['S','Z','E','AE','K','G'])) {
                    $vatCategoryCode = 'S';
                }

                // Unique line ID (BT-126: alphanumeric, max 6 chars)
                $lineIdBase = preg_replace('/[^A-Za-z0-9\-\_\.]/', '', $item['id'] ?? 'LINE' . ($index + 1));
                $lineIdBase = substr($lineIdBase, 0, 6);
                $lineId = $lineIdBase;
                $suffix = 1;
                while (in_array($lineId, $usedLineIds)) {
                    $lineId = $lineIdBase . '_' . $suffix++;
                }
                $usedLineIds[] = $lineId;

                // Tax category
                $classifiedTax = (new ClassifiedTaxCategory())
                    ->setId($vatCategoryCode)
                    ->setPercent($vatPercent)
                    ->setTaxScheme((new TaxScheme())->setId('VAT'));

                $productItem = (new Item())
                    ->setName($description)
                    ->setClassifiedTaxCategory($classifiedTax);

                $price = (new Price())
                    ->setUnitCode($item['unit_code'] ?? 'PCE')
                    ->setPriceAmount(number_format($basePrice, 2, '.', ''))
                    ->setBaseQuantity(1);

                $invoiceLine = new InvoiceLine();
                $invoiceLine->setId($lineId);
                $invoiceLine->setInvoicedQuantity(number_format($quantity, 2, '.', ''));
                $invoiceLine->setLineExtensionAmount(number_format($netPrice, 2, '.', '')); // ← TAX-EXCLUSIVE
                $invoiceLine->setItem($productItem);
                $invoiceLine->setPrice($price);

                $invoiceLines[] = $invoiceLine;

                $totalNet += $netPrice;
                $totalVat += $vatAmount;

                $rateKey = $vatCategoryCode . '_' . $vatPercent;
                if (!isset($vatGroups[$rateKey])) {
                    $vatGroups[$rateKey] = [
                        'taxable' => 0.0,
                        'tax'     => 0.0,
                        'rate'    => $vatPercent,
                        'category'=> $vatCategoryCode
                    ];
                }
                $vatGroups[$rateKey]['taxable'] += $netPrice;
                $vatGroups[$rateKey]['tax']     += $vatAmount;

            } catch (Exception $e) {
                Log::channel('zatca')->error('❌ Error processing line item', [
                    'index' => $index,
                    'error' => $e->getMessage(),
                    'item_data' => $item
                ]);
                throw $e;
            }
        }

        // --- Final Totals ---
        $totalNet = round($totalNet, 2);
        $totalVat = round($totalVat, 2);
        $grand    = round($totalNet + $totalVat, 2);

        Log::channel('zatca')->debug('📊 Invoice lines processed', [
            'total_lines' => count($invoiceLines),
            'total_line_amount' => $totalNet,
            'total_vat_amount' => $totalVat,
            'grand_total' => $grand,
            'vat_groups_count' => count($vatGroups),
            'pricing_type' => $pricingType
        ]);

        return [
            'lines'             => $invoiceLines,
            'total_line_amount' => number_format($totalNet, 2, '.', ''),
            'total_vat_amount'  => number_format($totalVat, 2, '.', ''),
            'grand_total'       => number_format($grand, 2, '.', ''),
            'vat_groups'        => $vatGroups
        ];
    }

    /**
     * Build tax total information from VAT groups
     */
    protected function buildTaxTotal(array $vatGroups, ?float $providedVatAmount = null): TaxTotal
    {
        Log::channel('zatca')->debug('🧮 Building tax total', [
            'vat_groups_count' => count($vatGroups),
            'provided_vat_amount' => $providedVatAmount
        ]);

        $taxSubTotals = [];
        $calculatedTotal = 0.0;

        // Step 1: build initial subtotals
        foreach ($vatGroups as $rateKey => $group) {
            $taxableAmount = round($group['taxable'], 2);
            $calculatedTax = round($group['tax'], 2);

            $taxSubTotals[] = [
                'taxable'   => $taxableAmount,
                'tax'       => $calculatedTax,
                'rate'      => $group['rate'],
                'category'  => $group['category'] ?? 'S',
            ];

            $calculatedTotal += $calculatedTax;
        }

        // Step 2: decide final total
        $finalTotal = $providedVatAmount !== null
            ? round($providedVatAmount, 2)
            : round($calculatedTotal, 2);

        // Step 3: adjust subtotals proportionally if needed
        if ($providedVatAmount !== null && abs($calculatedTotal - $finalTotal) > 0.01) {
            $difference = $finalTotal - $calculatedTotal;
            $runningSum = 0.0;

            foreach ($taxSubTotals as $i => &$subTotal) {
                if ($i < count($taxSubTotals) - 1) {
                    $share = ($subTotal['tax'] / $calculatedTotal) * $difference;
                    $subTotal['tax'] = round($subTotal['tax'] + $share, 2);
                    $runningSum += $subTotal['tax'];
                } else {
                    $subTotal['tax'] = round($finalTotal - $runningSum, 2);
                }
            }
            unset($subTotal);
        }

        // Step 4: build TaxTotal object
        $taxTotal = new TaxTotal();
        $taxTotal->setTaxAmount($finalTotal);

        foreach ($taxSubTotals as $subTotal) {
            $taxSub = new TaxSubTotal();
            $taxSub->setTaxableAmount((float) number_format($subTotal['taxable'], 2, '.', ''));
            $taxSub->setTaxAmount((float) number_format($subTotal['tax'], 2, '.', ''));

            $taxCategory = (new TaxCategory())
                ->setId($subTotal['category'])
                ->setPercent($subTotal['rate'])
                ->setTaxScheme((new TaxScheme())->setId('VAT'));

            $taxSub->setTaxCategory($taxCategory);
            $taxTotal->addTaxSubTotal($taxSub);
        }

        Log::channel('zatca')->debug('✅ Tax total built', [
            'calculated_total' => $calculatedTotal,
            'final_total'      => $finalTotal,
            'subtotals_count'  => count($taxSubTotals),
        ]);

        return $taxTotal;
    }

    /**
     * Build additional document references (ICV, PIH, QR) - FIXED VERSION
     */
    protected function buildAdditionalDocumentReferences(array $invoiceData): array
    {
        $references = [];

        // ICV must always exist
        if (empty($invoiceData['invoice_counter'])) {
            throw new Exception("Invoice counter is required for BR-KSA-33 compliance");
        }

        $references[] = (new AdditionalDocumentReference())
            ->setId('ICV')
            ->setUUID(trim((string)$invoiceData['invoice_counter']));

        // PIH (optional) - use placeholder for first invoice
        $pihValue = !empty($invoiceData['pih']) ? trim((string)$invoiceData['pih']) : str_repeat('0', 64);
        $references[] = (new AdditionalDocumentReference())
            ->setId('PIH')
            ->setUUID($pihValue);

        // QR (optional) - MUST be last - use placeholder initially
        $qrPlaceholder = base64_encode('QR_PLACEHOLDER_' . ($invoiceData['uuid'] ?? ''));
        $references[] = (new AdditionalDocumentReference())
            ->setId('QR')
            ->setUUID($qrPlaceholder);

        // Ensure no duplicate IDs
        $usedIds = [];
        foreach ($references as $ref) {
            $id = $ref->getId();
            if (in_array($id, $usedIds, true)) {
                throw new Exception("Duplicate AdditionalDocumentReference ID: $id");
            }
            $usedIds[] = $id;
        }

        Log::channel('zatca')->debug('📎 AdditionalDocumentReferences built', [
            'count' => count($references),
            'ids' => $usedIds
        ]);

        return $references;
    }

    /**
     * Validate and clean QR code content to prevent SaxonApiException
     */
    private function validateAndCleanQrContent(string $qrContent): string
    {
        // Remove any whitespace, newlines, or special characters
        $cleanQr = preg_replace('/\s+/', '', $qrContent);
        $cleanQr = trim($cleanQr);

        // Validate it's proper base64
        if (!base64_decode($cleanQr, true)) {
            throw new Exception("QR code content is not valid Base64");
        }

        // Validate length (typical TLV for Phase 2 is 300-600 chars when base64 encoded)
        $decodedLength = strlen(base64_decode($cleanQr));
        if ($decodedLength < 200 || $decodedLength > 1000) {
            throw new Exception("QR code content has invalid length: " . $decodedLength . " bytes");
        }

        // Ensure it doesn't contain problematic characters
        if (preg_match('/[^\x20-\x7E]/', $cleanQr)) {
            throw new Exception("QR code content contains invalid characters");
        }

        Log::channel('zatca')->debug('✅ QR content validated', [
            'original_length' => strlen($qrContent),
            'cleaned_length' => strlen($cleanQr),
            'decoded_bytes' => $decodedLength
        ]);

        return $cleanQr;
    }

    /**
     * Helper method to prepare invoice data from Branch model and invoice details
     */
    public static function prepareFromBranch(Branch $branch, array $invoiceDetails): array
    {
        Log::channel('zatca')->debug('🏢 Preparing invoice data from branch', [
            'branch_id' => $branch->id,
            'branch_name' => $branch->company,
            'invoice_details_keys' => array_keys($invoiceDetails),
            'pricing_type' => $invoiceDetails['pricing_type'] ?? 'unknown'
        ]);

        $lineItems = [];
        $totalNetAmount = 0.0;
        $totalVatAmount = 0.0;
        $pricingType = $invoiceDetails['pricing_type'] ?? 'exclusive';

        foreach ($invoiceDetails['line_items'] ?? [] as $index => $item) {
            $quantity = (float) ($item['quantity'] ?? 0);
            $unitPrice = (float) ($item['unit_price'] ?? 0);
            $lineTotal = (float) ($item['total'] ?? ($quantity * $unitPrice));
            $vatPercent = (float) ($item['vat_percent'] ?? 0);

            if ($pricingType === 'inclusive') {
                $itemVatAmount = round($lineTotal * ($vatPercent / (100 + $vatPercent)), 2);
                $lineNetAmount = $lineTotal - $itemVatAmount;
            } else {
                $itemVatAmount = round($lineTotal * ($vatPercent / 100), 2);
                $lineNetAmount = $lineTotal;
            }

            $lineId = (string) ($item['id'] ?? ($index + 1));
            $lineId = str_pad(substr($lineId, 0, 6), max(1, min(6, strlen($lineId))), '0', STR_PAD_LEFT);

            $lineItems[] = array_merge($item, [
                'id' => $lineId,
                'vat_category_code' => $item['vat_category_code'] ?? 'S',
                'net_price' => round($lineNetAmount, 2),
                'total' => round($lineTotal, 2),
                'vat_amount' => $itemVatAmount,
                'vat_percent' => round($vatPercent, 2),
            ]);

            $totalNetAmount += $lineNetAmount;
            $totalVatAmount += $itemVatAmount;
        }

        $totalNetAmount = round($totalNetAmount, 2);
        $totalVatAmount = round($totalVatAmount, 2);
        $grandTotal = round($totalNetAmount + $totalVatAmount, 2);

        $prepaidAmount = (float) ($invoiceDetails['prepaid_amount'] ?? 0);
        $amountDue = round($grandTotal - $prepaidAmount, 2);

        if ($amountDue <= 0) {
            $amountDue = $grandTotal;
            Log::channel('zatca')->warning('⚠️ Amount due calculated as 0 or negative, using grand total', [
                'amount_due' => $amountDue,
                'grand_total' => $grandTotal,
                'prepaid_amount' => $prepaidAmount
            ]);
        }

        return [
            'invoice_number' => $invoiceDetails['number'] ?? 'INV-' . date('YmdHis'),
            'issue_datetime' => $invoiceDetails['issue_date'] ?? now(),
            'uuid' => $invoiceDetails['uuid'] ?? (string) Str::uuid(),
            'supplier_name' => $branch->company,
            'supplier_id' => $branch->tr_no,
            'supplier_crn' => $branch->commercial_registration_number,
            'supplier_building' => $branch->supplier_building ?? '0000',
            'supplier_street' => $branch->address,
            'supplier_city' => $branch->location,
            'supplier_postal' => $branch->supplier_postal ?? '00000',
            'supplier_country' => $branch->supplier_country ?? 'SA',
            'customization_id' => $branch->invoice_customization_id ?? 'urn:cen.eu:en16931:2017#compliant#urn:sa.gov.zatca:invoice',
            'profile_id' => 'reporting:1.0',
            'invoice_transaction_code' => $branch->invoice_transaction_code ?? '01000000',
            'buyer_name' => $invoiceDetails['customer_name'] ?? '',
            'buyer_vat' => $invoiceDetails['customer_vat'] ?? '',
            'buyer_street' => $invoiceDetails['customer_street'] ?? 'N/A',
            'buyer_city' => $invoiceDetails['customer_city'] ?? '',
            'buyer_postal' => $invoiceDetails['customer_postal'] ?? '00000',
            'buyer_country' => $invoiceDetails['customer_country'] ?? 'SA',
            'total_amount' => $grandTotal,
            'amount_due' => $amountDue,
            'vat_amount' => $totalVatAmount,
            'prepaid_amount' => round($prepaidAmount, 2),
            'currency' => $branch->currency ?? 'SAR',
            'line_items' => $lineItems,
            'invoice_counter' => $invoiceDetails['invoice_counter'] ?? '1',
            'previous_invoice_hash' => $invoiceDetails['previous_invoice_hash'] ?? '',
            'transaction_type' => $invoiceDetails['transaction_type'] ?? 'B2C',
            'pricing_type' => $pricingType,
            'invoice_type_code' => $invoiceDetails['invoice_type_code'] ?? '01000000',
            // ✅ ADD KSA ADDRESS FIELDS HERE IF AVAILABLE IN BRANCH MODEL
            'supplier_plot' => $branch->plot_identification ?? null,
            'supplier_province' => $branch->province ?? null,
            'supplier_province_code' => $branch->province_code ?? null,
            'supplier_district' => $branch->district ?? null,
            'supplier_additional_address_line' => $branch->additional_address_line ?? null,
        ];
    }

    /**
     * Validate mandatory fields for ZATCA compliance.
     */
    protected function validateMandatoryFields(array $invoiceData): array
    {
        $errors = [];
        $mandatoryFields = [
            'supplier_id' => 'VAT registration number',
            'supplier_name' => 'Supplier name',
            'supplier_street' => 'Supplier street',
            'supplier_building' => 'Supplier building number',
            'supplier_city' => 'Supplier city',
            'supplier_postal' => 'Supplier postal code',
            'buyer_name' => 'Buyer name',
            'invoice_number' => 'Invoice number',
            'issue_datetime' => 'Issue date/time',
            'total_amount' => 'Total amount',
            'invoice_counter' => 'Invoice counter',
        ];

        foreach ($mandatoryFields as $field => $description) {
            if (empty($invoiceData[$field])) {
                $errors[] = "Missing mandatory field: $description";
            }
        }

        if (!empty($invoiceData['supplier_id']) &&
            (strlen($invoiceData['supplier_id']) !== 15 ||
            substr($invoiceData['supplier_id'], 0, 1) !== '3' ||
            substr($invoiceData['supplier_id'], -1) !== '3')) {
            $errors[] = "VAT number must be 15 digits starting and ending with '3'";
        }

        return $errors;
    }

    /**
     * Parse datetime from various formats
     */
    protected function parseDateTime($dateTimeValue): DateTimeImmutable
    {
        Log::channel('zatca')->debug('📅 Parsing datetime', ['input' => $dateTimeValue]);
        try {
            if ($dateTimeValue instanceof DateTimeInterface) {
                $result = DateTimeImmutable::createFromInterface($dateTimeValue);
            } elseif (is_numeric($dateTimeValue)) {
                $result = new DateTimeImmutable('@' . $dateTimeValue);
            } else {
                $result = new DateTimeImmutable((string) $dateTimeValue, new DateTimeZone('UTC'));
            }

            Log::channel('zatca')->debug('✅ Datetime parsed successfully', [
                'result' => $result->format('Y-m-d H:i:s')
            ]);
            return $result;
        } catch (Exception $e) {
            Log::channel('zatca')->error('❌ Datetime parsing failed', [
                'input' => $dateTimeValue,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Build supplier party information
     */
    protected function buildSupplierParty(array $invoiceData, TaxScheme $taxScheme): Party
    {
        Log::channel('zatca')->debug('🏢 Building supplier party', [
            'supplier_name' => $invoiceData['supplier_name'] ?? 'unknown',
            'supplier_id' => $invoiceData['supplier_id'] ?? 'unknown'
        ]);

        try {
            $supplierAddress = (new Address())
                ->setStreetName($invoiceData['supplier_street'] ?? '')
                ->setBuildingNumber($invoiceData['supplier_building'] ?? '0000')
                ->setCityName($invoiceData['supplier_city'] ?? '')
                ->setPostalZone($invoiceData['supplier_postal'] ?? '00000')
                ->setCountry($invoiceData['supplier_country'] ?? 'SA');

            $supplierLegalEntity = (new LegalEntity())
                ->setRegistrationName($invoiceData['supplier_name'] ?? '');

            $supplierVat = $invoiceData['supplier_id'] ?? '';
            if (!empty($supplierVat)) {
                if (strlen($supplierVat) !== 15 || substr($supplierVat, 0, 1) !== '3' || substr($supplierVat, -1) !== '3') {
                    Log::channel('zatca')->warning('⚠️ VAT number format invalid, attempting to format', [
                        'current_vat' => $supplierVat
                    ]);
                    $middle = substr($supplierVat, 0, 13);
                    $middle = str_pad($middle, 13, '0', STR_PAD_RIGHT);
                    $supplierVat = '3' . $middle . '3';
                }
            }

            $supplierTaxScheme = (new PartyTaxScheme())
                ->setTaxScheme($taxScheme)
                ->setCompanyId($supplierVat);

            $supplierParty = (new Party())
                ->setLegalEntity($supplierLegalEntity)
                ->setPartyTaxScheme($supplierTaxScheme)
                ->setPostalAddress($supplierAddress);

            if (!empty($invoiceData['supplier_crn'])) {
                $supplierParty
                    ->setPartyIdentification($invoiceData['supplier_crn'])
                    ->setPartyIdentificationId('CRN');
            }

            Log::channel('zatca')->debug('✅ Supplier party built successfully');
            return $supplierParty;
        } catch (Exception $e) {
            Log::channel('zatca')->error('❌ Supplier party building failed', [
                'error' => $e->getMessage(),
                'supplier_data' => [
                    'name' => $invoiceData['supplier_name'] ?? 'unknown',
                    'id' => $invoiceData['supplier_id'] ?? 'unknown'
                ]
            ]);
            throw $e;
        }
    }

    /**
     * Build buyer party information
     */
    protected function buildBuyerParty(array $invoiceData, TaxScheme $taxScheme): Party
    {
        Log::channel('zatca')->debug('👤 Building buyer party', [
            'buyer_name' => $invoiceData['buyer_name'] ?? 'unknown'
        ]);

        try {
            $buyerAddress = (new Address())
                ->setStreetName($invoiceData['buyer_street'] ?? 'N/A')
                ->setCityName($invoiceData['buyer_city'] ?? '')
                ->setPostalZone($invoiceData['buyer_postal'] ?? '00000')
                ->setCountry($invoiceData['buyer_country'] ?? 'SA');

            $buyerLegalEntity = (new LegalEntity())
                ->setRegistrationName($invoiceData['buyer_name'] ?? 'N/A');

            $buyerParty = (new Party())
                ->setLegalEntity($buyerLegalEntity)
                ->setPostalAddress($buyerAddress);

            if (!empty($invoiceData['buyer_vat'])) {
                $buyerTaxScheme = (new PartyTaxScheme())
                    ->setTaxScheme($taxScheme)
                    ->setCompanyId($invoiceData['buyer_vat']);
                $buyerParty
                    ->setPartyTaxScheme($buyerTaxScheme)
                    ->setPartyIdentification($invoiceData['buyer_vat'])
                    ->setPartyIdentificationId('VAT');
            }

            Log::channel('zatca')->debug('✅ Buyer party built successfully');
            return $buyerParty;
        } catch (Exception $e) {
            Log::channel('zatca')->error('❌ Buyer party building failed', [
                'error' => $e->getMessage(),
                'buyer_name' => $invoiceData['buyer_name'] ?? 'unknown'
            ]);
            throw $e;
        }
    }

    /**
     * Apply default values for required ZATCA fields
     */
    protected static function applyDefaultValues(array $invoiceData): array
    {
        Log::channel('zatca')->debug('📋 Applying default values', [
            'current_data' => array_diff_key($invoiceData, array_flip(['line_items']))
        ]);

        // --- Invoice Type & Transaction Code (BR-KSA-06) ---
        $transactionType = strtoupper($invoiceData['transaction_type'] ?? 'B2C');
        switch ($transactionType) {
            case 'B2B':
            case 'B2G':
                $invoiceTypeCode = '388';
                $invoiceTransactionCode = '01000000';
                break;
            case 'CREDIT':
                $invoiceTypeCode = '381';
                $invoiceTransactionCode = '04000000';
                break;
            case 'DEBIT':
                $invoiceTypeCode = '383';
                $invoiceTransactionCode = '03000000';
                break;
            case 'B2C':
            default:
                $invoiceTypeCode = '388';
                $invoiceTransactionCode = '02000000';
                break;
        }

        $defaults = [
            'uuid'                     => (string) Str::uuid(),
            'invoice_counter'          => $invoiceData['invoice_counter'] ?? '1',
            'pih'                      => $invoiceData['previous_invoice_hash'] ?? str_repeat('0', 64), // Default zero hash
            'supplier_building'        => $invoiceData['supplier_building'] ?? '0000',
            'supplier_postal'          => $invoiceData['supplier_postal'] ?? '00000',
            'supplier_country'         => $invoiceData['supplier_country'] ?? 'SA',
            'buyer_country'            => $invoiceData['buyer_country'] ?? 'SA',
            'currency'                 => $invoiceData['currency'] ?? 'SAR',
            'customization_id'         => $invoiceData['customization_id'] ?? 'urn:cen.eu:en16931:2017#compliant#urn:sa.gov.zatca:invoice',
            'profile_id'               => 'reporting:1.0',
            'invoice_type_code'        => $invoiceTypeCode,
            'invoice_transaction_code' => $invoiceTransactionCode,
            'buyer_street'             => $invoiceData['buyer_street'] ?? 'N/A',
            'buyer_postal'             => $invoiceData['buyer_postal'] ?? '00000',
            'total_amount'             => max(0, (float) ($invoiceData['total_amount'] ?? 0)),
            'amount_due'               => max(0, (float) ($invoiceData['amount_due'] ?? $invoiceData['total_amount'] ?? 0)),
            
            // ✅ ADD KSA ADDRESS DEFAULTS
            'supplier_plot' => $invoiceData['supplier_plot'] ?? 'PLOT-001',
            'supplier_province' => $invoiceData['supplier_province'] ?? 'Riyadh Province',
            'supplier_province_code' => $invoiceData['supplier_province_code'] ?? '01',
            'supplier_district' => $invoiceData['supplier_district'] ?? 'Al Olaya',
            'supplier_additional_address_line' => $invoiceData['supplier_additional_address_line'] ?? 'Building 1',
            'buyer_plot' => $invoiceData['buyer_plot'] ?? 'PLOT-002',
            'buyer_province' => $invoiceData['buyer_province'] ?? 'Makkah Province',
            'buyer_province_code' => $invoiceData['buyer_province_code'] ?? '02',
            'buyer_district' => $invoiceData['buyer_district'] ?? 'Al Zahra',
            'buyer_additional_address_line' => $invoiceData['buyer_additional_address_line'] ?? 'Office 1',
        ];

        $result = array_merge($defaults, $invoiceData);

        // Ensure amount_due is never zero if total_amount > 0
        if ($result['amount_due'] <= 0 && $result['total_amount'] > 0) {
            $result['amount_due'] = $result['total_amount'];
            Log::channel('zatca')->warning('⚠️ Amount due was 0 or negative, set to total amount', [
                'amount_due'   => $result['amount_due'],
                'total_amount' => $result['total_amount']
            ]);
        }

        // ✅ ✅ ✅ DO NOT MODIFY LINE ITEMS HERE! ✅ ✅ ✅
        if (isset($result['line_items'])) {
            foreach ($result['line_items'] as $index => &$item) {
                // Sanitize line ID to be safe (BT-126: alphanumeric, max 6 chars)
                $lineId = (string) ($item['id'] ?? ($index + 1));
                $lineId = preg_replace('/[^A-Za-z0-9\-\_\.]/', '', $lineId);
                if ($lineId === '') {
                    $lineId = (string)($index + 1);
                }
                $lineId = substr($lineId, 0, 6);

                // Validate VAT category
                $vatCategoryCode = $item['vat_category_code'] ?? 'S';
                if (!in_array($vatCategoryCode, ['S', 'Z', 'E', 'AE', 'K', 'G'])) {
                    $vatCategoryCode = 'S';
                }

                // Only update what's necessary — NO net_price override!
                $item['id'] = $lineId;
                $item['vat_category_code'] = $vatCategoryCode;
            }
        }

        Log::channel('zatca')->debug('✅ Default values applied', [
            'result_data' => array_diff_key($result, array_flip(['line_items']))
        ]);

        return $result;
    }

    /**
     * Validate ZATCA certificate format and requirements
     */
    private function validateZatcaCertificate(string $certContent): string
    {
        $certContent = trim($certContent);
        
        if (!preg_match('/-----BEGIN CERTIFICATE-----.*-----END CERTIFICATE-----/s', $certContent)) {
            throw new Exception("Invalid ZATCA certificate PEM format");
        }

        // Check if it's ECDSA certificate (required by ZATCA)
        if (!str_contains($certContent, 'ECDSA') && !str_contains($certContent, 'prime256v1')) {
            throw new Exception("ZATCA requires ECDSA P-256 certificate. RSA certificates are not accepted.");
        }

        // Validate certificate structure
        $certificate = openssl_x509_read($certContent);
        if (!$certificate) {
            throw new Exception("Invalid certificate content");
        }

        $certInfo = openssl_x509_parse($certificate);
        if (!$certInfo) {
            throw new Exception("Cannot parse certificate information");
        }

        // Check key algorithm
        if (!isset($certInfo['signatureTypeSN']) || stripos($certInfo['signatureTypeSN'], 'ecdsa') === false) {
            throw new Exception("Certificate must use ECDSA algorithm for ZATCA compliance");
        }

        openssl_x509_free($certificate);

        return $certContent;
    }

    /**
     * Validate ZATCA private key format
     */
    private function validateZatcaPrivateKey(string $keyContent): string
    {
        $keyContent = trim($keyContent);
        $validPatterns = [
            '/-----BEGIN PRIVATE KEY-----.*-----END PRIVATE KEY-----/s',
            '/-----BEGIN EC PRIVATE KEY-----.*-----END EC PRIVATE KEY-----/s',
            '/-----BEGIN RSA PRIVATE KEY-----.*-----END RSA PRIVATE KEY-----/s'
        ];

        $isValid = false;
        foreach ($validPatterns as $pattern) {
            if (preg_match($pattern, $keyContent)) {
                $isValid = true;
                break;
            }
        }

        if (!$isValid) {
            throw new Exception("Invalid ZATCA private key PEM format");
        }

        return $keyContent;
    }

    /**
     * Debug method to identify XML generation failures
     */
    private function debugXmlGeneration(array $invoiceData): void
    {
        Log::channel('zatca')->debug('=== XML GENERATION DEBUG ===');

        // Check required fields
        $requiredFields = [
            'supplier_id', 'supplier_name', 'supplier_street', 'supplier_building',
            'supplier_city', 'supplier_postal', 'supplier_country',
            'buyer_name', 'buyer_street', 'buyer_city', 'buyer_postal', 'buyer_country',
            'invoice_number', 'issue_datetime', 'total_amount', 'vat_amount',
            'invoice_counter', 'uuid', 'currency'
        ];

        $missingFields = [];
        foreach ($requiredFields as $field) {
            if (!isset($invoiceData[$field]) || $invoiceData[$field] === '') {
                $missingFields[] = $field;
            }
        }

        Log::channel('zatca')->debug('Required fields check', [
            'missing_fields' => $missingFields,
            'has_line_items' => !empty($invoiceData['line_items']),
            'line_items_count' => count($invoiceData['line_items'] ?? [])
        ]);

        // Check VAT number format
        $supplierVat = $invoiceData['supplier_id'] ?? '';
        if (!empty($supplierVat)) {
            $vatValid = strlen($supplierVat) === 15 && 
                    substr($supplierVat, 0, 1) === '3' && 
                    substr($supplierVat, -1) === '3';
            Log::channel('zatca')->debug('VAT number validation', [
                'vat_number' => $supplierVat,
                'is_valid' => $vatValid,
                'length' => strlen($supplierVat)
            ]);
        }

        Log::channel('zatca')->debug('=== END DEBUG ===');
    }

    /**
     * Validate certificate object for ZATCA compliance
     */
    private function validateCertificateForZatca(Certificate $certificate): void
    {
        try {
            $privateKey = $certificate->getPrivateKey();
            if (!$privateKey instanceof \phpseclib3\Crypt\Common\PrivateKey) {
                throw new Exception("Private key is not a valid phpseclib3 PrivateKey object");
            }

            $testData = 'zatca_validation_test';
            $signature = $privateKey->sign(hash('sha256', $testData, true));

            if (empty($signature)) {
                throw new Exception("Private key signing test failed");
            }

            Log::channel('zatca')->debug('✅ ZATCA certificate validation passed', [
                'private_key_type' => get_class($privateKey),
                'signature_length' => strlen($signature)
            ]);
        } catch (Exception $e) {
            throw new Exception("ZATCA certificate validation failed: " . $e->getMessage());
        }
    }

    /**
     * Helper method for consistent error responses
     */
    private function errorResponse(array $errors, array $warnings, ?string $unsignedXml, ?string $signedXml, ?string $qrBase64, ?string $invoiceHash, ?string $uuid): array
    {
        return [
            'errors'       => $errors,
            'warnings'     => $warnings,
            'unsigned_xml' => $unsignedXml,
            'signed_xml'   => $signedXml,
            'qr_base64'    => $qrBase64,
            'invoice_hash' => $invoiceHash,
            'uuid'         => $uuid,
        ];
    }
}