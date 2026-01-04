<?php
namespace Saleh7\Zatca;

use Sabre\Xml\Reader;
use Sabre\Xml\Writer;
use Sabre\Xml\XmlDeserializable;
use Sabre\Xml\XmlSerializable;
use function Sabre\Xml\Deserializer\keyValue;

class AdditionalDocumentReference implements XmlSerializable, XmlDeserializable
{
    private ?string $id = null;
    private ?string $uuid = null;
    private ?string $documentTypeCode = null;
    private ?string $documentDescription = null;
    private ?string $documentIssueDate = null;
    private ?string $documentIssueTime = null;
    private ?string $documentNumber = null;
    private ?Attachment $attachment = null;

    /**
     * @return string|null
     */
    public function getId(): ?string
    {
        return $this->id;
    }

    /**
     * @param string|null $id
     * @return static
     */
    public function setId(?string $id): self
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getUUID(): ?string
    {
        return $this->uuid;
    }

    /**
     * @param string|null $uuid
     * @return static
     */
    public function setUUID(?string $uuid): self
    {
        $this->uuid = $uuid;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getDocumentTypeCode(): ?string
    {
        return $this->documentTypeCode;
    }

    /**
     * @param string|null $documentTypeCode
     * @return static
     */
    public function setDocumentTypeCode(?string $documentTypeCode): self
    {
        $this->documentTypeCode = $documentTypeCode;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getDocumentDescription(): ?string
    {
        return $this->documentDescription;
    }

    /**
     * @param string|null $documentDescription
     * @return static
     */
    public function setDocumentDescription(?string $documentDescription): self
    {
        $this->documentDescription = $documentDescription;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getDocumentIssueDate(): ?string
    {
        return $this->documentIssueDate;
    }

    /**
     * @param string|null $documentIssueDate
     * @return static
     */
    public function setDocumentIssueDate(?string $documentIssueDate): self
    {
        $this->documentIssueDate = $documentIssueDate;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getDocumentIssueTime(): ?string
    {
        return $this->documentIssueTime;
    }

    /**
     * @param string|null $documentIssueTime
     * @return static
     */
    public function setDocumentIssueTime(?string $documentIssueTime): self
    {
        $this->documentIssueTime = $documentIssueTime;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getDocumentNumber(): ?string
    {
        return $this->documentNumber;
    }

    /**
     * @param string|null $documentNumber
     * @return static
     */
    public function setDocumentNumber(?string $documentNumber): self
    {
        $this->documentNumber = $documentNumber;
        return $this;
    }

    /**
     * @return Attachment|null
     */
    public function getAttachment(): ?Attachment
    {
        return $this->attachment;
    }

    /**
     * @param Attachment|null $attachment
     * @return static
     */
    public function setAttachment(?Attachment $attachment): self
    {
        $this->attachment = $attachment;
        return $this;
    }

    /**
     * The xmlSerialize method is called during xml writing.
     *
     * @param Writer $writer
     * @return void
     */
    public function xmlSerialize(Writer $writer): void
    {
        // Required: <cbc:ID>
        if ($this->id !== null) {
            $writer->write([
                Schema::CBC . 'ID' => $this->id
            ]);
        }

        // Optional: <cbc:UUID> — used for ICV, PIH
        if ($this->uuid !== null) {
            $writer->write([
                Schema::CBC . 'UUID' => $this->uuid
            ]);
        }

        // Required for QR: <cbc:DocumentTypeCode>
        if ($this->documentTypeCode !== null) {
            $writer->write([
                Schema::CBC . 'DocumentTypeCode' => $this->documentTypeCode
            ]);
        }

        // Required for QR: <cbc:DocumentDescription>
        if ($this->documentDescription !== null) {
            $writer->write([
                Schema::CBC . 'DocumentDescription' => $this->documentDescription
            ]);
        }

        // Required for QR: <cbc:DocumentIssueDate>
        if ($this->documentIssueDate !== null) {
            $writer->write([
                Schema::CBC . 'DocumentIssueDate' => $this->documentIssueDate
            ]);
        }

        // Required for QR: <cbc:DocumentIssueTime>
        if ($this->documentIssueTime !== null) {
            $writer->write([
                Schema::CBC . 'DocumentIssueTime' => $this->documentIssueTime
            ]);
        }

        // Required for QR: <cbc:DocumentNumber>
        if ($this->documentNumber !== null) {
            $writer->write([
                Schema::CBC . 'DocumentNumber' => $this->documentNumber
            ]);
        }

        // Required for QR: <cac:Attachment> with embeddedDocumentBinaryObject
        if ($this->attachment !== null) {
            $writer->write([
                Schema::CAC . 'Attachment' => $this->attachment
            ]);
        }
    }

    /**
     * The xmlDeserialize method is called during xml reading.
     * @param Reader $xml
     * @return static
     */
    public static function xmlDeserialize(Reader $reader)
    {
        $keyValues = keyValue($reader);

        return (new static())
            ->setId($keyValues[Schema::CBC . 'ID'] ?? null)
            ->setUUID($keyValues[Schema::CBC . 'UUID'] ?? null)
            ->setDocumentTypeCode($keyValues[Schema::CBC . 'DocumentTypeCode'] ?? null)
            ->setDocumentDescription($keyValues[Schema::CBC . 'DocumentDescription'] ?? null)
            ->setDocumentIssueDate($keyValues[Schema::CBC . 'DocumentIssueDate'] ?? null)
            ->setDocumentIssueTime($keyValues[Schema::CBC . 'DocumentIssueTime'] ?? null)
            ->setDocumentNumber($keyValues[Schema::CBC . 'DocumentNumber'] ?? null)
            ->setAttachment($keyValues[Schema::CAC . 'Attachment'] ?? null);
    }
}