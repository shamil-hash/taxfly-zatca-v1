<?php

namespace App\Services;

use Saleh7\Zatca\GeneratorInvoice;
use Saleh7\Zatca\InvoiceSigner;
use Saleh7\Zatca\Invoice;
use Saleh7\Zatca\InvoiceLine;
use Saleh7\Zatca\Item;
use Saleh7\Zatca\Price;
use Saleh7\Zatca\Party;
use Saleh7\Zatca\PartyTaxScheme;
use Saleh7\Zatca\LegalEntity;
use Saleh7\Zatca\TaxScheme;
use Saleh7\Zatca\TaxCategory;
use Saleh7\Zatca\TaxSubTotal;
use Saleh7\Zatca\TaxTotal;
use Saleh7\Zatca\LegalMonetaryTotal;
use Saleh7\Zatca\UnitCode;
use Saleh7\Zatca\Helpers\Certificate;
use Saleh7\Zatca\Helpers\QRCodeGenerator;
use Saleh7\Zatca\Storage;
use DateTime;

class ZatcaService
{
    protected string $storagePath;
    protected Certificate $certificate;

    public function __construct()
    {
        $this->storagePath = storage_path('certs');
        $certFile = $this->storagePath . '/dev_cert.pem';
        $keyFile = $this->storagePath . '/dev_key.pem';

        if (!is_readable($certFile) || !is_readable($keyFile)) {
            throw new \RuntimeException("Certificate or private key not readable.");
        }

        $this->certificate = new Certificate($certFile, $keyFile, null); // 3rd argument optional secret if needed
    }

    /**
     * Generate Invoice Object (ZATCA-compliant)
     */
    public function generateInvoice(array $data): Invoice
    {
        $invoice = new Invoice();
        $invoice->setId($data['invoice_number']);
        $invoice->setIssueDate(new DateTime($data['issue_date']));
        $invoice->setIssueTime(new DateTime($data['issue_time']));
        $invoice->setInvoiceCurrencyCode('SAR');

        // Supplier Party
        $supplier = new Party();
        $supplier->setPartyIdentification($data['seller_vat']);
        $supplier->setLegalEntity((new LegalEntity())->setRegistrationName($data['seller_name']));
        $supplier->setPartyTaxScheme(
            (new PartyTaxScheme())->setCompanyID($data['seller_vat'])->setTaxScheme((new TaxScheme())->setID('VAT'))
        );
        $invoice->setAccountingSupplierParty($supplier);

        // Customer Party
        $customer = new Party();
        $customer->setPartyIdentification($data['customer_vat']);
        $customer->setLegalEntity((new LegalEntity())->setRegistrationName($data['customer_name']));
        $customer->setPartyTaxScheme(
            (new PartyTaxScheme())->setCompanyID($data['customer_vat'])->setTaxScheme((new TaxScheme())->setID('VAT'))
        );
        $invoice->setAccountingCustomerParty($customer);

        // Invoice Lines
        $lines = [];
        $totalTax = 0;
        $totalAmount = 0;
        $lineNo = 1;

        foreach ($data['lines'] as $lineData) {
            $line = new InvoiceLine();
            $line->setId($lineNo++);
            $line->setInvoicedQuantity($lineData['quantity']);
            $line->setLineExtensionAmount($lineData['quantity'] * $lineData['unit_price']);

            $item = new Item();
            $item->setName($lineData['description']);
            $line->setItem($item);

            $price = new Price();
            $price->setPriceAmount($lineData['unit_price']);
            $price->setUnitCode(UnitCode::UNIT);
            $line->setPrice($price);

            $taxAmount = $lineData['unit_price'] * $lineData['quantity'] * ($lineData['tax_rate'] / 100);
            $taxSubTotal = (new TaxSubTotal())
                ->setTaxableAmount($lineData['unit_price'] * $lineData['quantity'])
                ->setTaxAmount($taxAmount)
                ->setTaxCategory((new TaxCategory())->setPercent($lineData['tax_rate'])->setTaxScheme((new TaxScheme())->setID('VAT')));
            $line->setTaxTotal((new TaxTotal())->addTaxSubTotal($taxSubTotal)->setTaxAmount($taxAmount));

            $totalTax += $taxAmount;
            $totalAmount += $lineData['unit_price'] * $lineData['quantity'] + $taxAmount;

            $lines[] = $line;
        }

        $invoice->setInvoiceLines($lines);
        $invoice->setTaxTotal((new TaxTotal())->setTaxAmount($totalTax));
        $invoice->setLegalMonetaryTotal((new LegalMonetaryTotal())
            ->setLineExtensionAmount($totalAmount - $totalTax)
            ->setTaxExclusiveAmount($totalAmount - $totalTax)
            ->setTaxInclusiveAmount($totalAmount)
        );

        return $invoice;
    }

    /**
     * Generate XML string and save
     */
    public function generateInvoiceXML(Invoice $invoice): string
    {
        $generator = new GeneratorInvoice();
        $xmlString = $generator->generate($invoice);

        $filePath = $this->storagePath . '/unsigned_' . $invoice->getId() . '.xml';
        (new Storage())->put($filePath, $xmlString);

        return $filePath;
    }

    /**
     * Sign XML invoice
     */
    public function signInvoice(string $unsignedXmlPath): string
    {
        $xml = (new Storage())->get($unsignedXmlPath);
        $signedXml = InvoiceSigner::signInvoice($xml, $this->certificate);

        $signedPath = str_replace('unsigned_', 'signed_', $unsignedXmlPath);
        (new Storage())->put($signedPath, $signedXml);

        return $signedPath;
    }

    /**
     * Generate QR Code PNG path
     */
    public function generateQRCode(string $signedXmlPath): string
    {
        $signedXml = (new Storage())->get($signedXmlPath);
        $qr = new QRCodeGenerator();
        $qrPath = $this->storagePath . '/qr_' . basename($signedXmlPath, '.xml') . '.png';
        $qr->generateFromSignedXML($signedXml, $qrPath);
        return $qrPath;
    }

    /**
     * Decode QR content
     */
    public function decodeQRCode(string $signedXmlPath): string
    {
        $signedXml = (new Storage())->get($signedXmlPath);
        $qr = new QRCodeGenerator();
        return $qr->decodeFromSignedXML($signedXml);
    }
}
