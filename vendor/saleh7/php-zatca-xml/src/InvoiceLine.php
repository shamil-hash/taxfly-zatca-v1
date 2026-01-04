<?php
namespace Saleh7\Zatca;

use InvalidArgumentException;
use Sabre\Xml\Writer;
use Sabre\Xml\XmlSerializable;

/**
 * Class InvoiceLine
 *
 * Represents an invoice line item and provides XML serialization.
 *
 * FIXED: Removed invalid <TaxScheme> output in xmlSerialize when Price is null.
 */
class InvoiceLine implements XmlSerializable
{
    /** @var string|null Identifier for the invoice line. */
    private ?string $id = null;

    /** @var float|null Invoiced quantity. */
    private ?float $invoicedQuantity = null;

    /** @var float|null Line extension amount. */
    private ?float $lineExtensionAmount = null;

    /** @var AllowanceCharge[]|null Array of allowance charge objects. */
    private ?array $allowanceCharges = null;

    /** @var DocumentReference|null Document reference. */
    private ?DocumentReference $documentReference = null;

    /** @var string Unit code (default 'MON'). */
    private string $unitCode = 'MON';

    /** @var TaxTotal|null Tax total details for the line. */
    private ?TaxTotal $taxTotal = null;

    /** @var InvoicePeriod|null Invoice period. */
    private ?InvoicePeriod $invoicePeriod = null;

    /** @var string|null Note for the line. */
    private ?string $note = null;

    /** @var Item|null Item details. */
    private ?Item $item = null;

    /** @var Price|null Price details. */
    private ?Price $price = null;

    /** @var string|null Accounting cost code. */
    private ?string $accountingCostCode = null;

    /** @var string|null Accounting cost. */
    private ?string $accountingCost = null;

    // --- Getters (Unchanged) ---

    /**
     * Get the invoice line ID.
     *
     * @return string|null
     */
    public function getId(): ?string
    {
        return $this->id;
    }

    /**
     * Get the invoiced quantity.
     *
     * @return float|null
     */
    public function getInvoicedQuantity(): ?float
    {
        return $this->invoicedQuantity;
    }

    /**
     * Get the line extension amount.
     *
     * @return float|null
     */
    public function getLineExtensionAmount(): ?float
    {
        return $this->lineExtensionAmount;
    }

    /**
     * Get the allowance charges.
     *
     * @return AllowanceCharge[]|null
     */
    public function getAllowanceCharges(): ?array
    {
        return $this->allowanceCharges;
    }

    /**
     * Get the document reference.
     *
     * @return DocumentReference|null
     */
    public function getDocumentReference(): ?DocumentReference
    {
        return $this->documentReference;
    }

    /**
     * Get the unit code.
     *
     * @return string
     */
    public function getUnitCode(): string
    {
        return $this->unitCode;
    }

    /**
     * Get the tax total.
     *
     * @return TaxTotal|null
     */
    public function getTaxTotal(): ?TaxTotal
    {
        return $this->taxTotal;
    }

    /**
     * Get the note.
     *
     * @return string|null
     */
    public function getNote(): ?string
    {
        return $this->note;
    }

    /**
     * Get the invoice period.
     *
     * @return InvoicePeriod|null
     */
    public function getInvoicePeriod(): ?InvoicePeriod
    {
        return $this->invoicePeriod;
    }

    /**
     * Get the item.
     *
     * @return Item|null
     */
    public function getItem(): ?Item
    {
        return $this->item;
    }

    /**
     * Get the price.
     *
     * @return Price|null
     */
    public function getPrice(): ?Price
    {
        return $this->price;
    }

    /**
     * Get the accounting cost code.
     *
     * @return string|null
     */
    public function getAccountingCostCode(): ?string
    {
        return $this->accountingCostCode;
    }

    /**
     * Get the accounting cost.
     *
     * @return string|null
     */
    public function getAccountingCost(): ?string
    {
        return $this->accountingCost;
    }

    // --- Setters (Unchanged) ---

    /**
     * Set the invoice line ID.
     *
     * @param string|null $id
     * @return self
     * @throws InvalidArgumentException if ID is empty.
     */
    public function setId(?string $id): self
    {
        if ($id !== null && trim($id) === '') {
            throw new InvalidArgumentException('Invoice line ID cannot be empty.');
        }
        $this->id = $id;
        return $this;
    }

    /**
     * Set the invoiced quantity.
     *
     * @param float|null $invoicedQuantity
     * @return self
     * @throws InvalidArgumentException if quantity is negative.
     */
    public function setInvoicedQuantity(?float $invoicedQuantity): self
    {
        if ($invoicedQuantity !== null && $invoicedQuantity < 0) {
            throw new InvalidArgumentException('Invoiced quantity must be non-negative.');
        }
        $this->invoicedQuantity = $invoicedQuantity;
        return $this;
    }

    /**
     * Set the line extension amount.
     *
     * @param float|null $lineExtensionAmount
     * @return self
     * @throws InvalidArgumentException if amount is negative.
     */
    public function setLineExtensionAmount(?float $lineExtensionAmount): self
    {
        if ($lineExtensionAmount !== null && $lineExtensionAmount < 0) {
            throw new InvalidArgumentException('Line extension amount must be non-negative.');
        }
        $this->lineExtensionAmount = $lineExtensionAmount;
        return $this;
    }

    /**
     * Set the allowance charges.
     *
     * @param AllowanceCharge[]|null $allowanceCharges
     * @return self
     */
    public function setAllowanceCharges(?array $allowanceCharges): self
    {
        $this->allowanceCharges = $allowanceCharges;
        return $this;
    }

    /**
     * Set the document reference.
     *
     * @param DocumentReference|null $documentReference
     * @return self
     */
    public function setDocumentReference(?DocumentReference $documentReference): self
    {
        $this->documentReference = $documentReference;
        return $this;
    }

    /**
     * Set the unit code.
     *
     * @param string|null $unitCode
     * @return self
     * @throws InvalidArgumentException if unit code is empty.
     */
    public function setUnitCode(?string $unitCode): self
    {
        if ($unitCode !== null && trim($unitCode) === '') {
            throw new InvalidArgumentException('Unit code cannot be empty.');
        }
        $this->unitCode = $unitCode ?? $this->unitCode;
        return $this;
    }

    /**
     * Set the tax total.
     *
     * @param TaxTotal|null $taxTotal
     * @return self
     */
    public function setTaxTotal(?TaxTotal $taxTotal): self
    {
        $this->taxTotal = $taxTotal;
        return $this;
    }

    /**
     * Set the note.
     *
     * @param string|null $note
     * @return self
     * @throws InvalidArgumentException if note is provided as an empty string.
     */
    public function setNote(?string $note): self
    {
        if ($note !== null && trim($note) === '') {
            throw new InvalidArgumentException('Note cannot be empty if provided.');
        }
        $this->note = $note;
        return $this;
    }

    /**
     * Set the invoice period.
     *
     * @param InvoicePeriod|null $invoicePeriod
     * @return self
     */
    public function setInvoicePeriod(?InvoicePeriod $invoicePeriod): self
    {
        $this->invoicePeriod = $invoicePeriod;
        return $this;
    }

    /**
     * Set the item.
     *
     * @param Item $item
     * @return self
     */
    public function setItem(Item $item): self
    {
        $this->item = $item;
        return $this;
    }

    /**
     * Set the price.
     *
     * @param Price|null $price
     * @return self
     */
    public function setPrice(?Price $price): self
    {
        $this->price = $price;
        return $this;
    }

    /**
     * Set the accounting cost code.
     *
     * @param string|null $accountingCostCode
     * @return self
     * @throws InvalidArgumentException if provided as an empty string.
     */
    public function setAccountingCostCode(?string $accountingCostCode): self
    {
        if ($accountingCostCode !== null && trim($accountingCostCode) === '') {
            throw new InvalidArgumentException('Accounting cost code cannot be empty.');
        }
        $this->accountingCostCode = $accountingCostCode;
        return $this;
    }

    /**
     * Set the accounting cost.
     *
     * @param string|null $accountingCost
     * @return self
     * @throws InvalidArgumentException if provided as an empty string.
     */
    public function setAccountingCost(?string $accountingCost): self
    {
        if ($accountingCost !== null && trim($accountingCost) === '') {
            throw new InvalidArgumentException('Accounting cost cannot be empty.');
        }
        $this->accountingCost = $accountingCost;
        return $this;
    }

    /**
     * XML serialize the invoice line.
     *
     * FIXED: Removed the incorrect 'else' block that wrote <TaxScheme> when Price was null.
     *        Now, Price is only written if it exists, maintaining correct UBL structure.
     *
     * @param Writer $writer The XML writer.
     * @return void
     */
    public function xmlSerialize(Writer $writer): void
    {
        // Write ID element
        $writer->write([
            Schema::CBC . 'ID' => $this->id
        ]);

        // Write Note element if provided
        if (!empty($this->getNote())) {
            $writer->write([
                Schema::CBC . 'Note' => $this->getNote()
            ]);
        }

        // Write InvoicedQuantity and LineExtensionAmount elements with attributes
        $writer->write([
            [
                'name'       => Schema::CBC . 'InvoicedQuantity',
                'value'      => number_format($this->invoicedQuantity ?? 0, 6, '.', ''),
                'attributes' => [
                    'unitCode' => $this->unitCode
                ]
            ],
            [
                'name'       => Schema::CBC . 'LineExtensionAmount',
                'value'      => number_format($this->lineExtensionAmount ?? 0, 2, '.', ''),
                'attributes' => [
                    'currencyID' => GeneratorInvoice::$currencyID
                ]
            ]
        ]);

        // Write each AllowanceCharge element if available
        if ($this->allowanceCharges !== null) {
            foreach ($this->allowanceCharges as $allowanceCharge) {
                $writer->write([
                    Schema::CAC . 'AllowanceCharge' => $allowanceCharge
                ]);
            }
        }

        // Write DocumentReference element if available
        if ($this->documentReference !== null) {
            $writer->write([
                Schema::CAC . 'DocumentReference' => $this->documentReference
            ]);
        }

        // Write AccountingCostCode if available
        if ($this->accountingCostCode !== null) {
            $writer->write([
                Schema::CBC . 'AccountingCostCode' => $this->accountingCostCode
            ]);
        }

        // Write AccountingCost if available
        if ($this->accountingCost !== null) {
            $writer->write([
                Schema::CBC . 'AccountingCost' => $this->accountingCost
            ]);
        }

        // Write InvoicePeriod element if available
        if ($this->invoicePeriod !== null) {
            $writer->write([
                Schema::CAC . 'InvoicePeriod' => $this->invoicePeriod
            ]);
        }

        // Write TaxTotal element if available
        if ($this->taxTotal !== null) {
            $writer->write([
                Schema::CAC . 'TaxTotal' => $this->taxTotal
            ]);
        }

        // Write Item element (mandatory)
        // Ensured that the Item object is correctly serialized within cac:Item tags
        $writer->write([
            Schema::CAC . 'Item' => $this->item,
        ]);

        // Write Price element ONLY if it is available.
        // This fixes the XSD_SCHEMA_ERROR caused by the incorrect <TaxScheme> output.
        if ($this->price !== null) {
            $writer->write([
                Schema::CAC . 'Price' => $this->price
            ]);
        }
        // If Price is null, we simply do not write the <cac:Price> element.
        // This is valid UBL as Price is not always mandatory in every context,
        // though the KB InvoiceXmlGenerator should always provide it.
    }
}