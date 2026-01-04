<?php

namespace App\Services;

use DOMDocument;
use RuntimeException;

/**
 * Builds a minimal UBL 2.1 Invoice XML suitable for ZATCA testing.
 * This generator is intentionally conservative; you must expand and
 * adapt fields to match the exact ZATCA UBL profile and your structures.
 */
class ZatcaUblGenerator
{
    /**
     * Build UBL Invoice XML string from invoice array.
     *
     * Expected $invoice array keys (examples):
     * - id, number, issue_date (YYYY-MM-DD), currency
     * - supplier (array): name, vat_number, address...
     * - customer (array)
     * - lines (array of arrays): description, quantity, unit_price, line_extension_amount, tax_amount
     * - tax_total, legal_monetary_total (with payableAmount)
     */
    public static function generate(array $invoice): string
    {
        $doc = new DOMDocument('1.0', 'UTF-8');
        $doc->preserveWhiteSpace = false;
        $doc->formatOutput = false;

        $ns_ubl = 'urn:oasis:names:specification:ubl:schema:xsd:Invoice-2';
        $ns_cac = 'urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2';
        $ns_cbc = 'urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2';
        $ns_ext = 'urn:oasis:names:specification:ubl:schema:xsd:CommonExtensionComponents-2';

        $invoiceEl = $doc->createElementNS($ns_ubl, 'Invoice');
        $invoiceEl->setAttribute('xmlns:cac', $ns_cac);
        $invoiceEl->setAttribute('xmlns:cbc', $ns_cbc);
        $invoiceEl->setAttribute('xmlns:ext', $ns_ext);

        // UBL Extensions placeholder
        $extEl = $doc->createElementNS($ns_ext, 'ext:UBLExtensions');
        $invoiceEl->appendChild($extEl);

        // UBLVersionID
        $invoiceEl->appendChild($doc->createElementNS($ns_cbc, 'cbc:UBLVersionID', '2.1'));
        $invoiceEl->appendChild($doc->createElementNS($ns_cbc, 'cbc:CustomizationID', 'urn:fdc:pe:ubl:customization:psvi:1')); // adapt as needed
        $invoiceEl->appendChild($doc->createElementNS($ns_cbc, 'cbc:ID', $invoice['number'] ?? ('INV-' . $invoice['id'] ?? '')));
        $invoiceEl->appendChild($doc->createElementNS($ns_cbc, 'cbc:IssueDate', $invoice['issue_date'] ?? date('Y-m-d')));

        // Accounting Supplier Party (seller)
        $supplier = $invoice['supplier'] ?? [];
        $supplierParty = $doc->createElementNS($ns_cac, 'cac:AccountingSupplierParty');
        $party = $doc->createElementNS($ns_cac, 'cac:Party');
        $partyName = $doc->createElementNS($ns_cac, 'cac:PartyName');
        $partyName->appendChild($doc->createElementNS($ns_cbc, 'cbc:Name', $supplier['name'] ?? ''));
        $party->appendChild($partyName);

        // Supplier Tax Registration
        if (!empty($supplier['vat_number'])) {
            $taxScheme = $doc->createElementNS($ns_cac, 'cac:PartyTaxScheme');
            $taxScheme->appendChild($doc->createElementNS($ns_cbc, 'cbc:CompanyID', $supplier['vat_number']));
            $party->appendChild($taxScheme);
        }

        $supplierParty->appendChild($party);
        $invoiceEl->appendChild($supplierParty);

        // Accounting Customer Party (buyer)
        $customer = $invoice['customer'] ?? [];
        $customerParty = $doc->createElementNS($ns_cac, 'cac:AccountingCustomerParty');
        $custParty = $doc->createElementNS($ns_cac, 'cac:Party');
        $custPartyName = $doc->createElementNS($ns_cac, 'cac:PartyName');
        $custPartyName->appendChild($doc->createElementNS($ns_cbc, 'cbc:Name', $customer['name'] ?? ''));
        $custParty->appendChild($custPartyName);
        $customerParty->appendChild($custParty);
        $invoiceEl->appendChild($customerParty);

        // Invoice lines
        foreach ($invoice['lines'] ?? [] as $idx => $line) {
            $lineEl = $doc->createElementNS($ns_cac, 'cac:InvoiceLine');
            $lineEl->appendChild($doc->createElementNS($ns_cbc, 'cbc:ID', (string)($idx + 1)));
            $lineEl->appendChild($doc->createElementNS($ns_cbc, 'cbc:InvoicedQuantity', number_format((float)($line['quantity'] ?? 1), 2, '.', '')));
            $lineEl->appendChild($doc->createElementNS($ns_cbc, 'cbc:LineExtensionAmount', number_format((float)($line['line_extension_amount'] ?? 0), 2, '.', '')));
            $item = $doc->createElementNS($ns_cac, 'cac:Item');
            $item->appendChild($doc->createElementNS($ns_cbc, 'cbc:Description', $line['description'] ?? ''));
            $lineEl->appendChild($item);

            $price = $doc->createElementNS($ns_cac, 'cac:Price');
            $price->appendChild($doc->createElementNS($ns_cbc, 'cbc:PriceAmount', number_format((float)($line['unit_price'] ?? 0), 2, '.', '')));
            $lineEl->appendChild($price);

            $invoiceEl->appendChild($lineEl);
        }

        // TaxTotal
        $taxTotal = $doc->createElementNS($ns_cac, 'cac:TaxTotal');
        $taxAmount = $doc->createElementNS($ns_cbc, 'cbc:TaxAmount', number_format((float)($invoice['tax_total'] ?? 0), 2, '.', ''));
        $taxTotal->appendChild($taxAmount);
        $invoiceEl->appendChild($taxTotal);

        // LegalMonetaryTotal
        $legalMonetaryTotal = $doc->createElementNS($ns_cac, 'cac:LegalMonetaryTotal');
        $legalMonetaryTotal->appendChild($doc->createElementNS($ns_cbc, 'cbc:PayableAmount', number_format((float)($invoice['legal_monetary_total']['payableAmount'] ?? 0), 2, '.', '')));
        $invoiceEl->appendChild($legalMonetaryTotal);

        $doc->appendChild($invoiceEl);

        return $doc->saveXML();
    }
}
