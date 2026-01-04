<?php

namespace App\Services\Zatca; // Ensure this matches the namespace expected by your autoloader and the InvoiceXmlGenerator

use Saleh7\Zatca\Invoice as BaseInvoice;
use Saleh7\Zatca\Schema;
// use Saleh7\Zatca\InvoiceType; // Not directly used in this override approach
use InvalidArgumentException;
use Sabre\Xml\Writer;
use DateTimeInterface;

/**
 * Class ZatcaInvoice
 *
 * Extends the base Saleh7\Zatca\Invoice to add ZATCA-specific customizations,
 * primarily for correctly serializing the KSA Invoice Transaction Code
 * into the 'name' attribute of the InvoiceTypeCode element.
 *
 * Updated to work with the InvoiceXmlGenerator from the Knowledge Base,
 * which passes invoice_type_code and invoice_transaction_code separately.
 */
class ZatcaInvoice extends BaseInvoice
{
    /** @var string Customization ID. */
    private string $customizationID = 'urn:cen.eu:en16931:2017#compliant#urn:sa.gov.zatca:invoice';

    /** @var string|null The core invoice type code (e.g., '388'). */
    private ?string $invoiceTypeCodeValue = null; // Renamed to avoid confusion

    /** @var string|null The KSA Invoice Transaction Code (e.g., '01000000'). */
    private ?string $ksaTransactionCode = null; // Specific property for the 'name' attribute

    /**
     * Get the customization ID.
     *
     * @return string
     */
    public function getCustomizationID(): string
    {
        return $this->customizationID;
    }

    /**
     * Set the customization ID.
     *
     * @param string $customizationID
     * @return self
     * @throws InvalidArgumentException
     */
    public function setCustomizationID(string $customizationID): self
    {
        if (trim($customizationID) === '') {
            throw new InvalidArgumentException('Customization ID cannot be empty.');
        }
        $this->customizationID = $customizationID;
        return $this;
    }

    /**
     * Set the invoice type code (BT-3).
     * Stores the code value. The 'name' attribute is set separately.
     *
     * @param string $invoiceTypeCode The UBL Invoice Type Code (e.g., '388').
     * @return self
     */
    public function setInvoiceTypeCode(string $invoiceTypeCode): self
    {
        $this->invoiceTypeCodeValue = $invoiceTypeCode;
        // Call parent if it needs the string code for any internal logic
        // parent::setInvoiceTypeCode($invoiceTypeCode);
        return $this;
    }

    /**
     * Get the invoice type code value.
     *
     * @return string|null
     */
    public function getInvoiceTypeCode(): ?string
    {
        return $this->invoiceTypeCodeValue;
    }

    /**
     * Set the KSA Invoice Transaction Code.
     * This value will be used as the 'name' attribute of <cbc:InvoiceTypeCode>.
     *
     * @param string $transactionCode The 8-character KSA transaction code (e.g., '01000000').
     * @return self
     */
    public function setInvoiceTypeCodeName(string $transactionCode): self
    {
        // Optional: Validate length/format here if needed
        // if (strlen($transactionCode) < 7 || strlen($transactionCode) > 9) {
        //     // Log warning or handle
        // }
        $this->ksaTransactionCode = $transactionCode;
        return $this;
    }

    /**
     * Get the KSA Invoice Transaction Code.
     *
     * @return string|null
     */
    public function getInvoiceTypeCodeName(): ?string
    {
        return $this->ksaTransactionCode;
    }


    /**
     * Custom XML serialization to ensure correct ZATCA Phase 2 structure.
     *
     * This method overrides the parent's xmlSerialize to:
     * 1.  Write elements in the correct UBL 2.1 order.
     * 2.  Correctly serialize the InvoiceTypeCode with its 'name' attribute.
     * 3.  Ensure all required elements are included.
     *
     * @param Writer $writer The Sabre XML writer.
     * @return void
     */
    public function xmlSerialize(Writer $writer): void
    {
        // It's good practice to call validation if the parent has one,
        // or implement validation logic here if needed.
        // $this->validate();

        // --- UBL/EN16931/ZATCA Required Order ---

        // CustomizationID (BT-24) - MUST BE FIRST
        $writer->write([
            Schema::CBC . 'CustomizationID' => $this->customizationID,
        ]);

        // ProfileID (BT-23)
        if ($this->getProfileID() !== null) {
            $writer->write([Schema::CBC . 'ProfileID' => $this->getProfileID()]);
        }

        // ID (BT-1)
        if ($this->getId() !== null) {
            $writer->write([Schema::CBC . 'ID' => $this->getId()]);
        }

        // UUID (BT-19)
        if ($this->getUUID() !== null) {
            $writer->write([Schema::CBC . 'UUID' => $this->getUUID()]);
        }

        // IssueDate (BT-2)
        if ($this->getIssueDate() !== null) {
            $writer->write([Schema::CBC . 'IssueDate' => $this->getIssueDate()->format('Y-m-d')]);
        }

        // IssueTime (BT-3) - Ensure 'Z' suffix for UTC if needed
        if ($this->getIssueTime() !== null) {
             $issueTime = $this->getIssueTime();
             if ($issueTime instanceof DateTimeInterface) {
                 // Ensure it's formatted as H:i:sZ for UTC
                 $writer->write([Schema::CBC . 'IssueTime' => $issueTime->format('H:i:s') . 'Z']);
             } else {
                 // Assume it's a pre-formatted string
                 $writer->write([Schema::CBC . 'IssueTime' => $issueTime]);
             }
        }

        // InvoiceTypeCode (BT-3) - CRITICAL FOR ZATCA: Include 'name' attribute
        // Use the stored code value and the KSA transaction code
        if ($this->invoiceTypeCodeValue !== null) {
            $invoiceTypeElement = [
                'name' => Schema::CBC . 'InvoiceTypeCode',
                'value' => $this->invoiceTypeCodeValue,
                'attributes' => []
            ];
            // Add the 'name' attribute if the KSA transaction code is present
            if ($this->ksaTransactionCode !== null) {
                $invoiceTypeElement['attributes']['name'] = $this->ksaTransactionCode;
            }
            $writer->write([$invoiceTypeElement]);
        }
        // If invoiceTypeCodeValue is null, the element is not written.
        // The parent validation or ZATCA rules might require it.

        // Note (BT-22) - Optional
        if ($this->getNote() !== null) {
            $writer->write([
                [
                    "name" => Schema::CBC . 'Note',
                    "value" => $this->getNote(),
                    "attributes" => [
                        "languageID" => 'en' // Default language, adjust if needed
                    ]
                ],
            ]);
        }

        // DocumentCurrencyCode (BT-5)
        if ($this->getDocumentCurrencyCode() !== null) {
            $writer->write([Schema::CBC . 'DocumentCurrencyCode' => $this->getDocumentCurrencyCode()]);
        }

        // TaxCurrencyCode (BT-6) - Required if different from DocumentCurrencyCode
        if ($this->getTaxCurrencyCode() !== null) {
            $writer->write([Schema::CBC . 'TaxCurrencyCode' => $this->getTaxCurrencyCode()]);
        }

        // OrderReference (BG-14) - Optional
        if ($this->getOrderReference() !== null) {
            $writer->write([Schema::CAC . 'OrderReference' => $this->getOrderReference()]);
        }

        // BillingReference(s) (BG-3) - Optional
        if ($this->getBillingReferences() !== null) {
            foreach ($this->getBillingReferences() as $billingReference) {
                $writer->write([
                    Schema::CAC . 'BillingReference' => $billingReference
                ]);
            }
        }

        // ContractDocumentReference (BG-20) - Optional
        if ($this->getContract() !== null) {
            $writer->write([
                Schema::CAC . 'ContractDocumentReference' => $this->getContract(),
            ]);
        }

        // AdditionalDocumentReference(s) (BG-24) - CRITICAL for ZATCA (ICV, PIH, QR)
        if ($this->getAdditionalDocumentReferences() !== null) {
            foreach ($this->getAdditionalDocumentReferences() as $additionalDocumentReference) {
                $writer->write([
                    Schema::CAC . 'AdditionalDocumentReference' => $additionalDocumentReference,
                ]);
            }
        }

        // Signature (BG-30) - Required for signed invoices, placeholder here
        if ($this->getSignature() !== null) {
            $writer->write([Schema::CAC . 'Signature' => $this->getSignature()]);
        }

        // AccountingSupplierParty (BG-4)
        if ($this->getAccountingSupplierParty() !== null) {
            $writer->write([
                Schema::CAC . 'AccountingSupplierParty' => [
                    Schema::CAC . 'Party' => $this->getAccountingSupplierParty(), // Assumes Party object
                ],
            ]);
        }

        // AccountingCustomerParty (BG-7)
        if ($this->getAccountingCustomerParty() !== null) {
            $writer->write([
                Schema::CAC . 'AccountingCustomerParty' => [
                    Schema::CAC . 'Party' => $this->getAccountingCustomerParty(), // Assumes Party object
                ],
            ]);
        }

        // Delivery (BG-13) - Optional
        if ($this->getDelivery() !== null) {
            $writer->write([Schema::CAC . 'Delivery' => $this->getDelivery()]);
        }

        // PaymentMeans (BG-16) - Optional
        if ($this->getPaymentMeans() !== null) {
            $writer->write([Schema::CAC . 'PaymentMeans' => $this->getPaymentMeans()]);
        }

        // AllowanceCharge(s) (BG-20/BG-21) - Optional
        if ($this->getAllowanceCharges() !== null) {
            foreach ($this->getAllowanceCharges() as $allowanceCharge) {
                $writer->write([
                    Schema::CAC . 'AllowanceCharge' => $allowanceCharge,
                ]);
            }
        }

        // TaxTotal (BG-22) - Required
        if ($this->getTaxTotal() !== null) {
            $writer->write([Schema::CAC . 'TaxTotal' => $this->getTaxTotal()]);
        }

        // LegalMonetaryTotal (BG-23) - Required
        if ($this->getLegalMonetaryTotal() !== null) {
            $writer->write([Schema::CAC . 'LegalMonetaryTotal' => $this->getLegalMonetaryTotal()]);
        }

        // InvoiceLine(s) (BG-25)
        if ($this->getInvoiceLines() !== null) {
            foreach ($this->getInvoiceLines() as $invoiceLine) {
                $writer->write([
                    Schema::CAC . 'InvoiceLine' => $invoiceLine,
                ]);
            }
        }
    }
}
