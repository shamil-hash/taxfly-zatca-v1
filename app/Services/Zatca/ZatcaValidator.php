<?php
namespace App\Services\Zatca;

use DOMDocument;
use DOMXPath;
use Illuminate\Support\Facades\Log;
use Exception;

class ZatcaValidator
{
    protected array $errors = [];
    protected array $warnings = [];
    protected array $info = [];

    protected float $EPSILON = 0.005;
    protected int $currencyDecimals = 2;
    protected ?string $logChannel = 'zatca';

    // Allowed invoice type codes (subset of UNTDID 1001 used by ZATCA)
    protected array $allowedInvoiceTypeCodes = [
        '388' => 'Tax Invoice',
        '381' => 'Credit Note',
        '383' => 'Debit Note',
        '386' => 'Prepayment Invoice',
        '389' => 'Self-billed Invoice',
        '326' => 'Partial invoice',
        '380' => 'Commercial Invoice',
        '384' => 'Corrected invoice',
    ];

    // Valid transaction codes (KSA-2 format)
    protected array $validTransactionCodes = [
        '01000000', '02000000', '03000000', '04000000'
    ];

    public function __construct(array $options = [])
    {
        if (isset($options['currencyDecimals'])) $this->currencyDecimals = (int)$options['currencyDecimals'];
        if (isset($options['epsilon'])) $this->EPSILON = (float)$options['epsilon'];
        if (isset($options['logChannel'])) $this->logChannel = $options['logChannel'];
        
        // Debug: Log constructor call
        if ($this->logChannel) {
            try {
                Log::channel($this->logChannel)->debug('ZatcaValidator constructed', ['options' => $options]);
            } catch (\Throwable $e) {
                // ignore logging errors
            }
        }
    }

    public function validate($input, array $options = []): array
    {
        // Merge runtime options
        if (isset($options['currencyDecimals'])) $this->currencyDecimals = (int)$options['currencyDecimals'];
        if (isset($options['epsilon'])) $this->EPSILON = (float)$options['epsilon'];
        if (array_key_exists('logChannel', $options)) $this->logChannel = $options['logChannel'];

        $this->errors = [];
        $this->warnings = [];
        $this->info = [];

        // Debug: Log validation start
        if ($this->logChannel) {
            try {
                $inputType = is_array($input) ? 'array' : (is_string($input) ? 'xml' : gettype($input));
                Log::channel($this->logChannel)->debug('ZatcaValidator validation started', ['input_type' => $inputType]);
            } catch (\Throwable $e) {
                // ignore logging errors
            }
        }

        if (is_array($input)) {
            $result = $this->validateData($input);
        } elseif (is_string($input)) {
            $result = $this->validateXml($input, $options);
        } else {
            throw new \InvalidArgumentException('ZatcaValidator::validate expects array or XML string');
        }

        // log results if there are issues
        if ((!empty($result['errors']) || !empty($result['warnings'])) && $this->logChannel) {
            try {
                Log::channel($this->logChannel)->warning('ZATCA validation', [$result]);
            } catch (\Throwable $e) {
                // ignore logging errors
            }
        }

        return $result;
    }

    public function validateData(array $data): array
    {
        // Reset
        $this->errors = [];
        $this->warnings = [];
        $this->info = [];

        // Debug: Log data being validated
        if ($this->logChannel) {
            try {
                Log::channel($this->logChannel)->debug('ZatcaValidator validating data', [
                    'invoice_type_code' => $data['invoice_type_code'] ?? 'NOT_SET',
                    'invoice_transaction_code' => $data['invoice_transaction_code'] ?? $data['transaction_code'] ?? 'NOT_SET'
                ]);
            } catch (\Throwable $e) {
                // ignore logging errors
            }
        }

        // BR-04: Invoice must have type code (BT-3)
        // --- START OF CHANGES ---
        // if (empty($data['invoice_type_code'])) { // OLD CHECK
        if (empty($data['invoice_type_code'])) { // IMPROVED CHECK
            $this->errors[] = 'BR-04: Invoice type code (BT-3) is mandatory.';
        } else {
            // CRITICAL FIX: Cast input to string to match type of $validTypeCodes array keys
            // This prevents issues if the input is an integer (e.g., 388) while the array keys are strings (e.g., '388').
            $invoiceTypeCode = (string)($data['invoice_type_code'] ?? ''); // Cast input to string
            $validTypeCodes = array_map('strval', array_keys($this->allowedInvoiceTypeCodes));
            
            // Debug: Log validation details (updated for clarity)
            if ($this->logChannel) {
                try {
                    Log::channel($this->logChannel)->debug('Invoice type code validation', [
                        'provided' => $invoiceTypeCode,
                        'provided_type' => gettype($data['invoice_type_code'] ?? null), // Log original type
                        'valid_codes' => $validTypeCodes,
                        'is_valid' => in_array($invoiceTypeCode, $validTypeCodes, true) // Use cast value
                    ]);
                } catch (\Throwable $e) {
                    // ignore logging errors
                }
            }
            
            // BR-CL-01: Invoice type code must be valid UNTDID 1001 code
            // The check now uses the cast $invoiceTypeCode variable.
            if (!in_array($invoiceTypeCode, $validTypeCodes, true)) {
                $this->errors[] = "BR-CL-01: Invoice type code '{$invoiceTypeCode}' must be a valid UNTDID 1001 code. Valid codes: " . implode(', ', $validTypeCodes);
            }
        }
        // --- END OF CHANGES ---

        // BR-KSA-06: Invoice must have transaction code
        $transactionCode = $data['invoice_transaction_code'] ?? $data['transaction_code'] ?? null;
        if (empty($transactionCode)) {
            $this->errors[] = 'BR-KSA-06: Invoice transaction code (KSA-2) is mandatory.';
        } else {
            $transactionCode = (string)$transactionCode;
            
            // Debug: Log validation details
            if ($this->logChannel) {
                try {
                    Log::channel($this->logChannel)->debug('Transaction code validation', [
                        'provided' => $transactionCode,
                        'valid_codes' => $this->validTransactionCodes,
                        'is_valid' => in_array($transactionCode, $this->validTransactionCodes, true)
                    ]);
                } catch (\Throwable $e) {
                    // ignore logging errors
                }
            }
            
            // BR-KSA-06: Transaction code must be valid format
            if (!in_array($transactionCode, $this->validTransactionCodes, true)) {
                $this->errors[] = "BR-KSA-06: Invoice transaction code '{$transactionCode}' must be a valid ZATCA transaction code. Valid codes: " . implode(', ', $this->validTransactionCodes);
            }
        }

        // Seller required fields (KSA mandatory address elements)
        if (empty($data['supplier_street'] ?? $data['seller_street'] ?? null)) {
            $this->errors[] = 'BR-KSA-09: Seller address must contain street name (supplier_street).';
        }
        if (empty($data['supplier_building'] ?? $data['seller_building'] ?? null)) {
            $this->errors[] = 'BR-KSA-10: Seller address must contain building number (supplier_building).';
        }
        if (empty($data['supplier_city'] ?? $data['seller_city'] ?? null)) {
            $this->errors[] = 'BR-KSA-11: Seller address must contain city name (supplier_city).';
        }
        if (empty($data['supplier_postal'] ?? $data['seller_postal'] ?? null)) {
            $this->errors[] = 'BR-KSA-12: Seller address must contain postal code (supplier_postal).';
        }
        if (empty($data['supplier_id'] ?? $data['seller_vat'] ?? $data['seller_trn'] ?? null)) {
            $this->errors[] = 'BR-KSA-13: Seller VAT registration number (supplier_id) is mandatory.';
        }

        // Buyer checks
        if (empty($data['buyer_name'] ?? $data['customer_name'] ?? null)) {
            $this->errors[] = 'BR-KSA-14: Buyer name is mandatory.';
        }
        $transactionType = strtoupper($data['transaction_type'] ?? 'B2C');
        if ($transactionType === 'B2B' && empty($data['buyer_vat'] ?? $data['customer_vat'] ?? null)) {
            $this->errors[] = 'BR-KSA-15: Buyer VAT number is mandatory for B2B invoices.';
        }

        // UUID and issue datetime
        if (empty($data['uuid'] ?? null)) {
            $this->errors[] = 'BR-KSA-16: UUID is mandatory.';
        }
        if (empty($data['issue_datetime'] ?? ($data['issue_date'] ?? null))) {
            $this->errors[] = 'BR-KSA-17: Issue date and/or time is mandatory.';
        }

        // Currency
        $currency = $data['currency'] ?? 'SAR';
        if (empty($currency)) {
            $this->errors[] = 'BR-KSA-18: Currency code is mandatory.';
        } elseif (strtoupper($currency) !== 'SAR') {
            $this->warnings[] = 'BR-KSA-18: Currency is not SAR, ensure ISO 4217 compliance.';
        }

        // Lines
        $lines = $data['line_items'] ?? $data['lines'] ?? [];
        if (!is_array($lines) || count($lines) === 0) {
            $this->errors[] = 'BR-KSA-30: Invoice must have at least one line item.';
            return $this->result();
        }

        $sumLineNet = 0.0;
        $sumTax = 0.0;
        $taxByCategory = [];

        foreach ($lines as $idx => $ln) {
            $lineNo = $idx + 1;
            $id = $ln['id'] ?? $ln['line_id'] ?? null;
            if (empty($id)) {
                $this->errors[] = "BT-126/BR-21: Invoice line #{$lineNo} missing id.";
            } else {
                if (!is_string($id) && !is_numeric($id)) {
                    $this->errors[] = "BT-126: Invoice line #{$lineNo} id must be string or number.";
                } else {
                    $idStr = (string)$id;
                    if (strlen($idStr) < 1 || strlen($idStr) > 6) {
                        $this->errors[] = "BR-KSA-F-06-C17: Invoice line id '{$idStr}' must be 1..6 characters. Line: {$lineNo}";
                    }
                }
            }

            $name = $ln['description'] ?? $ln['name'] ?? null;
            if (empty($name)) {
                $this->errors[] = "BT-153/BR-25: Invoice line #{$lineNo} missing item name/description.";
            }

            $qty = isset($ln['quantity']) ? (float)$ln['quantity'] : (isset($ln['qty']) ? (float)$ln['qty'] : 1.0);
            if (!is_numeric($qty) || $qty <= 0) {
                $this->warnings[] = "BT-146: Invoice line #{$lineNo} has non-positive quantity (using 1).";
                $qty = 1.0;
            }

            // unit price
            if (!isset($ln['unit_price']) && !isset($ln['unitPrice']) && !isset($ln['price'])) {
                $this->errors[] = "BT-146: Invoice line #{$lineNo} missing unit price.";
                $unitPrice = 0.0;
            } else {
                $unitPrice = (float)($ln['unit_price'] ?? $ln['unitPrice'] ?? $ln['price']);
            }

            // tax percent or tax amount
            $taxPercent = isset($ln['tax_percent']) ? (float)$ln['tax_percent'] : (isset($ln['taxPercent']) ? (float)$ln['taxPercent'] : null);
            $taxAmount = isset($ln['tax_amount']) ? (float)$ln['tax_amount'] : (isset($ln['taxAmount']) ? (float)$ln['taxAmount'] : null);

            // compute line net
            if (isset($ln['line_net']) || isset($ln['lineNet'])) {
                $lineNet = (float)($ln['line_net'] ?? $ln['lineNet']);
            } else {
                $lineNet = $this->round($unitPrice * $qty);
            }

            $sumLineNet += $lineNet;

            // compute tax amount if missing
            if ($taxAmount === null) {
                $taxAmount = $taxPercent !== null ? $this->round($lineNet * $taxPercent / 100.0) : 0.0;
            }
            $sumTax += $taxAmount;
            $taxCat = strtoupper($ln['tax_category'] ?? $ln['taxCategory'] ?? $ln['tax_cat'] ?? ($taxPercent > 0 ? 'S' : 'Z'));

            $taxByCategory[$taxCat]['taxable'] = ($taxByCategory[$taxCat]['taxable'] ?? 0.0) + $lineNet;
            $taxByCategory[$taxCat]['tax'] = ($taxByCategory[$taxCat]['tax'] ?? 0.0) + $taxAmount;
        }

        // Document allowances/charges
        $allowances = $data['allowances'] ?? [];
        $charges = $data['charges'] ?? [];
        $sumAllowances = 0.0; $sumCharges = 0.0;
        foreach ($allowances as $a) {
            $sumAllowances += (float)($a['amount'] ?? $a['value'] ?? 0.0);
        }
        foreach ($charges as $c) {
            $sumCharges += (float)($c['amount'] ?? $c['value'] ?? 0.0);
        }

        $prepaid = (float)($data['prepaid_amount'] ?? $data['prepayment_amount'] ?? 0.0);
        $rounding = (float)($data['rounding_amount'] ?? $data['rounding'] ?? 0.0);

        $taxExclusive = $sumLineNet - $sumAllowances + $sumCharges;
        $taxInclusive = $taxExclusive + $sumTax;
        $payable = $taxInclusive - $prepaid + $rounding;

        // If totals provided in input, compare
        if (isset($data['total_tax_exclusive']) || isset($data['tax_exclusive'])) {
            $given = (float)($data['total_tax_exclusive'] ?? $data['tax_exclusive']);
            if (abs($given - $this->round($taxExclusive)) > $this->EPSILON) {
                $this->errors[] = "BT-109: Provided TaxExclusiveAmount ({$this->formatAmount($given)}) does not match computed ({$this->formatAmount($this->round($taxExclusive))}).";
            }
        }
        if (isset($data['total_tax']) || isset($data['tax_total'])) {
            $given = (float)($data['total_tax'] ?? $data['tax_total']);
            if (abs($given - $this->round($sumTax)) > $this->EPSILON) {
                $this->errors[] = "Tax total mismatch: provided {$this->formatAmount($given)} vs computed {$this->formatAmount($this->round($sumTax))}.";
            }
        }
        if (isset($data['payable_amount'])) {
            $given = (float)$data['payable_amount'];
            if (abs($given - $this->round($payable)) > $this->EPSILON) {
                $this->errors[] = "BT-115: Provided PayableAmount ({$this->formatAmount($given)}) does not match computed ({$this->formatAmount($this->round($payable))}).";
            }
        }

        // Prepayment check BR-KSA-80
        if ($prepaid > 0) {
            $sumTaxable = 0.0; $sumPreTax = 0.0;
            if (!empty($data['prepayment_lines']) && is_array($data['prepayment_lines'])) {
                foreach ($data['prepayment_lines'] as $pl) {
                    $sumTaxable += (float)($pl['taxable_amount'] ?? $pl['taxable'] ?? 0.0);
                    $sumPreTax += (float)($pl['tax_amount'] ?? $pl['tax'] ?? 0.0);
                }
            }
            $expectedPre = $sumTaxable + $sumPreTax;
            if (abs($expectedPre - $prepaid) > max(0.01, $this->EPSILON)) {
                $this->warnings[] = "BR-KSA-80: The Pre-Paid amount ({$this->formatAmount($prepaid)}) must equal the sum of prepayment taxable ({$this->formatAmount($sumTaxable)}) and prepayment tax ({$this->formatAmount($sumPreTax)}).";
            }
        }

        // Transaction code ↔ invoice_type_code mapping check if provided
        if (!empty($data['transaction_code']) && !empty($data['invoice_type_code'])) {
            $mapErr = $this->validateTransactionCodeMatchesInvoiceType((string)$data['transaction_code'], (string)$data['invoice_type_code']);
            if ($mapErr !== null) $this->errors[] = $mapErr;
        }

        return $this->result();
    }

    /**
     * Post-flight validation on UBL 2.1 XML string.
     * Performs full DOM/XPath checks: InvoiceTypeCode, KSA transaction code, invoice lines structure,
     * tax totals, legal monetary totals (BT-109/BT-112/BT-115), VAT breakdown, and optional XSD validation.
     *
     * Supported option keys: enableXsdValidation (bool), localXsdPath (string|null).
     */
    public function validateXml(string $xmlString, array $options = []): array
    {
        $this->errors = [];
        $this->warnings = [];
        $this->info = [];

        libxml_use_internal_errors(true);
        $dom = new DOMDocument();
        if (!$dom->loadXML($xmlString)) {
            $errs = libxml_get_errors();
            foreach ($errs as $e) $this->errors[] = 'XML parse error: ' . trim($e->message);
            libxml_clear_errors();
            return $this->result();
        }

        $xpath = new DOMXPath($dom);
        $xpath->registerNamespace('cbc', 'urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2');
        $xpath->registerNamespace('cac', 'urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2');
        $xpath->registerNamespace('ubl', 'urn:oasis:names:specification:ubl:schema:xsd:Invoice-2');

        // Helper to try multiple xpath queries (prefixed and local-name fallback)
        $try = function(array $exprs, $context = null) use ($xpath) {
            foreach ($exprs as $e) {
                $res = $xpath->query($e, $context);
                if ($res !== false && $res->length > 0) return $res;
            }
            // fallback using local-name() (slow but robust)
            foreach ($exprs as $e) {
                // transform expression to local-name based if possible
                $fallback = preg_replace('/(\\/\\/)?([a-zA-Z_]+):([a-zA-Z_]+)/', '//*[local-name()="$3"]', $e);
                $res = $xpath->query($fallback, $context);
                if ($res !== false && $res->length > 0) return $res;
            }
            return $xpath->query(''); // empty node list
        };

        // InvoiceTypeCode
        $itcNodes = $try(['/ubl:Invoice/cbc:InvoiceTypeCode', '//cbc:InvoiceTypeCode']);
        if ($itcNodes->length === 0) {
            $this->errors[] = 'BR-04 / BT-3: Missing cbc:InvoiceTypeCode.';
        } else {
            $itcNode = $itcNodes->item(0);
            $itc = trim($itcNode->nodeValue);
            if (!array_key_exists($itc, $this->allowedInvoiceTypeCodes)) {
                $this->errors[] = "BR-CL-01: InvoiceTypeCode '{$itc}' is not in the allowed UNTDID 1001 subset for KSA.";
            }
            $ksaAttr = $itcNode->attributes->getNamedItem('name');
            if ($ksaAttr) {
                $transactionCode = trim($ksaAttr->nodeValue);
                if (!preg_match('/^\d{7,9}$/', $transactionCode)) {
                    $this->errors[] = "BR-KSA-06: Invalid KSA transaction code format in InvoiceTypeCode@name: '{$transactionCode}'. Expected 7-9 digits.";
                } else {
                    $mapErr = $this->validateTransactionCodeMatchesInvoiceType($transactionCode, $itc);
                    if ($mapErr !== null) $this->errors[] = $mapErr;
                }
            } else {
                $this->warnings[] = 'BR-KSA-06: Missing KSA transaction code in InvoiceTypeCode@name.';
            }
        }

        // Invoice lines
        $lineNodes = $try(['/ubl:Invoice/cac:InvoiceLine', '//cac:InvoiceLine']);
        if ($lineNodes->length === 0) {
            $this->errors[] = 'BR-16 / BG-25: Invoice must contain at least one cac:InvoiceLine.';
            return $this->result();
        }

        $sumLineNet = 0.0;
        $taxByCategory = [];

        foreach ($lineNodes as $li => $ln) {
            $lineNo = $li + 1;
            // ID
            $idNode = $try(['./cbc:ID','./cbc:ID'], $ln);
            if ($idNode->length === 0) {
                $this->errors[] = 'BR-21/BT-126: InvoiceLine #{$lineNo} missing cbc:ID.';
                continue;
            }
            $lineId = trim($idNode->item(0)->nodeValue);
            if ($lineId === '' || strlen($lineId) < 1 || strlen($lineId) > 6) {
                $this->errors[] = "BR-KSA-F-06-C17/BT-126: InvoiceLine ID '{$lineId}' must be 1..6 chars. Line {$lineNo}.";
            }

            // LineExtensionAmount
            $lineAmountNode = $try(['./cbc:LineExtensionAmount','./cac:LineExtensionAmount','./cbc:LineExtensionAmount'], $ln);
            if ($lineAmountNode->length === 0) {
                $this->errors[] = "BR-24/BT-131: InvoiceLine '{$lineId}' missing cbc:LineExtensionAmount.";
                $lineAmount = 0.0;
            } else {
                $lineAmount = (float)$lineAmountNode->item(0)->nodeValue;
                $sumLineNet += $lineAmount;
            }

            // Item name
            $itemNameNode = $try(['./cac:Item/cbc:Name','./cac:Item/cbc:Description','./cac:Item/*[local-name()="Name"]'], $ln);
            if ($itemNameNode->length === 0 || trim($itemNameNode->item(0)->nodeValue) === '') {
                $this->errors[] = "BR-25/BT-153: InvoiceLine '{$lineId}' missing item name (cac:Item/cbc:Name).";
            }

            // Unit price
            $unitPriceNode = $try(['./cac:Price/cbc:PriceAmount','./cac:Price/*[local-name()="PriceAmount"]'], $ln);
            if ($unitPriceNode->length === 0) {
                $this->errors[] = "BR-26/BT-146: InvoiceLine '{$lineId}' missing unit price (cac:Price/cbc:PriceAmount).";
            }

            // Tax category (multiple possible placements)
            $taxCatNode = $try([
                './cac:ClassifiedTaxCategory/cbc:ID',
                './cac:TaxCategory/cbc:ID',
                './cac:TaxSubTotal/cac:TaxCategory/cbc:ID',
                './cac:TaxTotal/cac:TaxSubtotal/cac:TaxCategory/cbc:ID'
            ], $ln);
            if ($taxCatNode->length === 0) {
                $this->errors[] = "BT-151: InvoiceLine '{$lineId}' missing tax category id.";
                $taxCat = 'S';
            } else {
                $taxCat = strtoupper(trim($taxCatNode->item(0)->nodeValue));
            }

            // Tax amount on line
            $taxAmountNode = $try(['./cac:TaxSubTotal/cbc:TaxAmount','./cac:TaxTotal/cbc:TaxAmount'], $ln);
            if ($taxAmountNode->length > 0) {
                $taxAmt = (float)$taxAmountNode->item(0)->nodeValue;
            } else {
                // fallback compute from percent
                $percentNode = $try(['./cac:ClassifiedTaxCategory/cbc:Percent','./cac:TaxCategory/cbc:Percent'], $ln);
                $percent = ($percentNode->length > 0) ? (float)$percentNode->item(0)->nodeValue : 0.0;
                $taxAmt = $this->round(($lineAmount ?? 0.0) * $percent / 100.0);
            }

            $taxByCategory[$taxCat]['taxable'] = ($taxByCategory[$taxCat]['taxable'] ?? 0.0) + ($lineAmount ?? 0.0);
            $taxByCategory[$taxCat]['tax'] = ($taxByCategory[$taxCat]['tax'] ?? 0.0) + $taxAmt;
        }

        // Document-level TaxTotal and TaxSubtotal
        $taxTotalNodes = $try(['/ubl:Invoice/cac:TaxTotal','//cac:TaxTotal']);
        $docTaxByCategory = [];
        $docTaxTotalAmount = 0.0;
        if ($taxTotalNodes->length > 0) {
            foreach ($taxTotalNodes as $tt) {
                $amtNode = $xpath->query('./cbc:TaxAmount', $tt);
                if ($amtNode->length > 0) $docTaxTotalAmount += (float)$amtNode->item(0)->nodeValue;
                $subNodes = $xpath->query('./cac:TaxSubtotal', $tt);
                foreach ($subNodes as $st) {
                    $catNode = $xpath->query('./cac:TaxCategory/cbc:ID', $st);
                    if ($catNode->length === 0) $catNode = $xpath->query('./cac:TaxCategory/cac:ClassifiedTaxCategory/cbc:ID', $st);
                    $cat = ($catNode->length>0) ? strtoupper(trim($catNode->item(0)->nodeValue)) : 'S';
                    $taxableNode = $xpath->query('./cbc:TaxableAmount', $st);
                    $taxAmtNode = $xpath->query('./cbc:TaxAmount', $st);
                    $taxable = ($taxableNode->length>0) ? (float)$taxableNode->item(0)->nodeValue : 0.0;
                    $taxAmt = ($taxAmtNode->length>0) ? (float)$taxAmtNode->item(0)->nodeValue : 0.0;
                    $docTaxByCategory[$cat]['taxable'] = ($docTaxByCategory[$cat]['taxable'] ?? 0.0) + $taxable;
                    $docTaxByCategory[$cat]['tax'] = ($docTaxByCategory[$cat]['tax'] ?? 0.0) + $taxAmt;
                }
            }
        }

        // Compare taxByCategory (from lines) to docTaxByCategory
        foreach ($taxByCategory as $cat => $vals) {
            $docVals = $docTaxByCategory[$cat] ?? ['taxable' => 0.0, 'tax' => 0.0];
            if (abs($vals['tax'] - $docVals['tax']) > $this->EPSILON) {
                $this->errors[] = "BR-S-09: Tax mismatch for category '{$cat}': sum of line taxes={$this->formatAmount($vals['tax'])}, document TaxSubtotal tax={$this->formatAmount($docVals['tax'])}.";
            }
        }

        // LegalMonetaryTotal checks (BT-109, BT-112, BT-115)
        $legalNodes = $try(['/ubl:Invoice/cac:LegalMonetaryTotal','//cac:LegalMonetaryTotal']);
        if ($legalNodes->length === 0) {
            $this->errors[] = 'Missing cac:LegalMonetaryTotal (required for BT-109/BT-112/BT-115).';
        } else {
            $LM = $legalNodes->item(0);
            $bt109 = $this->getSingleNumber($xpath, './cbc:TaxExclusiveAmount', $LM) ?? $this->getSingleNumber($xpath, './cbc:LineExtensionAmount', $LM);
            $bt112 = $this->getSingleNumber($xpath, './cbc:TaxInclusiveAmount', $LM);
            $bt115 = $this->getSingleNumber($xpath, './cbc:PayableAmount', $LM);
            $bt113 = $this->getSingleNumber($xpath, './cbc:PrepaidAmount', $LM) ?? 0.0;
            $bt114 = $this->getSingleNumber($xpath, './cbc:RoundingAmount', $LM) ?? 0.0;

            // Document allowances/charges
            $acNodes = $try(['/ubl:Invoice/cac:AllowanceCharge','//cac:AllowanceCharge']);
            $sumAllowances = 0.0; $sumCharges = 0.0;
            if ($acNodes->length > 0) {
                foreach ($acNodes as $ac) {
                    $chargeIndicator = $this->getSingleNodeValue($xpath, './cbc:ChargeIndicator', $ac);
                    $amount = (float)($this->getSingleNodeValue($xpath, './cbc:Amount', $ac) ?? 0.0);
                    $isCharge = ($chargeIndicator !== null) ? (strtolower(trim($chargeIndicator)) === 'true' || trim($chargeIndicator) === '1') : false;
                    if ($isCharge) $sumCharges += $amount; else $sumAllowances += $amount;
                }
            }

            $recomputedTaxExclusive = $sumLineNet - $sumAllowances + $sumCharges;

            $docTaxAmount = $docTaxTotalAmount;
            $recomputedTaxInclusive = $recomputedTaxExclusive + $docTaxAmount;
            $recomputedPayable = $recomputedTaxInclusive - $bt113 + $bt114;

            if ($bt109 !== null && abs($bt109 - $this->round($recomputedTaxExclusive)) > $this->EPSILON) {
                $this->errors[] = "BT-109 mismatch: document TaxExclusiveAmount={$this->formatAmount($bt109)}, recomputed={$this->formatAmount($this->round($recomputedTaxExclusive))}.";
            }
            if ($bt112 !== null && abs($bt112 - $this->round($recomputedTaxInclusive)) > $this->EPSILON) {
                $this->errors[] = "BT-112 mismatch: document TaxInclusiveAmount={$this->formatAmount($bt112)}, recomputed={$this->formatAmount($this->round($recomputedTaxInclusive))}.";
            }
            if ($bt115 !== null && abs($bt115 - $this->round($recomputedPayable)) > $this->EPSILON) {
                $this->errors[] = "BT-115 mismatch: document PayableAmount={$this->formatAmount($bt115)}, recomputed={$this->formatAmount($this->round($recomputedPayable))}.";
            }
        }

        // IssueDate checks
        $issueDateNode = $try(['/ubl:Invoice/cbc:IssueDate','//cbc:IssueDate']);
        if ($issueDateNode->length > 0) {
            $issueDate = trim($issueDateNode->item(0)->nodeValue);
            $ts = strtotime($issueDate);
            if ($ts === false) {
                $this->warnings[] = "BT-2 IssueDate '{$issueDate}' could not be parsed.";
            } else {
                if ($ts > time() + 60) $this->warnings[] = "BT-2 IssueDate '{$issueDate}' is in the future.";
            }
        }

        // AdditionalDocumentReference: for credit/debit notes require reference to original invoice
        $typeCodeNode = $itcNode ?? null;
        if ($typeCodeNode && in_array(trim($typeCodeNode->nodeValue), ['381','383'])) {
            $adr = $try(['/ubl:Invoice/cac:AdditionalDocumentReference','//cac:AdditionalDocumentReference']);
            if ($adr->length === 0) {
                $this->warnings[] = 'AdditionalDocumentReference is recommended for credit/debit notes to reference the original invoice.';
            }
        }

        // Optional XSD validation
        $enableXsd = $options['enableXsdValidation'] ?? false;
        $localXsdPath = $options['localXsdPath'] ?? null;
        if ($enableXsd) {
            $schemaOk = $this->validateAgainstXsd($xmlString, $localXsdPath);
            if (!$schemaOk['ok']) {
                $this->errors[] = 'XSD schema validation failed.';
                foreach ($schemaOk['messages'] as $m) $this->warnings[] = $m;
            }
        }

        return $this->result();
    }

    /**
     * Validate transaction code mapping to invoice type. Returns null if ok, otherwise error string.
     */
    protected function validateTransactionCodeMatchesInvoiceType(string $transactionCode, string $invoiceTypeCode): ?string
    {
        if (!preg_match('/^(\d{2})/', $transactionCode, $m)) {
            return "BR-KSA-06: Transaction code '{$transactionCode}' invalid format.";
        }
        $subtype = $m[1];

        $invoiceTypeCode = (string)$invoiceTypeCode;
        $taxInvoiceTypes = ['388','386','380'];
        $creditTypes = ['381'];
        $debitTypes = ['383'];

        if (in_array($invoiceTypeCode, $taxInvoiceTypes, true) && !in_array($subtype, ['01','02'], true)) {
            return "BR-KSA-06: InvoiceTypeCode '{$invoiceTypeCode}' expects transaction code starting with '01' or '02' but got '{$subtype}'.";
        }
        if (in_array($invoiceTypeCode, $creditTypes, true) && !in_array($subtype, ['01','02'], true)) {
            return "BR-KSA-06: Credit note InvoiceTypeCode '{$invoiceTypeCode}' expects transaction code starting with '01' or '02'.";
        }

        if (strlen($transactionCode) >= 7) {
            $pos5 = $transactionCode[4];
            $pos7 = $transactionCode[6];
            if ($pos5 === '1' && $pos7 === '1') {
                return "BR-KSA-07: Self-billing (pos7=1) not allowed for export invoices (pos5=1).";
            }
        }

        return null;
    }

    /**
     * Run DOMDocument schema validation using a local XSD bundle or remote OASIS XSD.
     * Returns ['ok' => bool, 'messages' => array]
     */
    public function validateAgainstXsd(string $xmlString, ?string $localXsdPath = null): array
    {
        libxml_use_internal_errors(true);
        $dom = new DOMDocument();
        $dom->preserveWhiteSpace = false;
        $dom->formatOutput = false;
        $ok = $dom->loadXML($xmlString);
        $out = ['ok' => false, 'messages' => []];
        if (!$ok) {
            $errs = libxml_get_errors();
            foreach ($errs as $e) $out['messages'][] = trim($e->message);
            libxml_clear_errors();
            return $out;
        }

        try {
            if ($localXsdPath && file_exists($localXsdPath)) {
                $out['ok'] = (bool)$dom->schemaValidate($localXsdPath);
            } else {
                // Note: Removed extra spaces from the URL
                $oasisUrl = 'https://docs.oasis-open.org/ubl/os-UBL-2.1/xsd/maindoc/UBL-Invoice-2.1.xsd';
                $out['ok'] = (bool)$dom->schemaValidate($oasisUrl);
            }
        } catch (\Exception $e) {
            $out['ok'] = false;
            $out['messages'][] = 'Schema validation error: ' . $e->getMessage();
        }

        $errs = libxml_get_errors();
        foreach ($errs as $e) $out['messages'][] = trim($e->message);
        libxml_clear_errors();
        return $out;
    }

    protected function getSingleNumber(DOMXPath $xpath, string $expr, ?\DOMNode $contextNode = null): ?float
    {
        $node = $contextNode ? $xpath->query($expr, $contextNode) : $xpath->query($expr);
        if ($node && $node->length > 0) return (float)$node->item(0)->nodeValue;
        return null;
    }

    protected function getSingleNodeValue(DOMXPath $xpath, string $expr, ?\DOMNode $contextNode = null): ?string
    {
        $node = $contextNode ? $xpath->query($expr, $contextNode) : $xpath->query($expr);
        if ($node && $node->length > 0) return trim($node->item(0)->nodeValue);
        return null;
    }

    protected function round(float $value): float
    {
        return round($value, $this->currencyDecimals);
    }

    protected function formatAmount($v): string
    {
        return number_format((float)$v, $this->currencyDecimals, '.', '');
    }

    protected function result(): array
    {
        return [
            'errors' => array_values(array_unique($this->errors)),
            'warnings' => array_values(array_unique($this->warnings)),
            'info' => array_values(array_unique($this->info)),
        ];
    }
}