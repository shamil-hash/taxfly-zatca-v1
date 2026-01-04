<?php
namespace Saleh7\Zatca;

use Sabre\Xml\Reader;
use Sabre\Xml\Writer;
use Sabre\Xml\XmlDeserializable;
use Sabre\Xml\XmlSerializable;

/**
 * Class InvoicePeriod
 *
 * Represents the invoice period with optional start and end dates.
 */
class InvoicePeriod implements XmlSerializable, XmlDeserializable
{
    private ?string $startDate = null;
    private ?string $endDate = null;

    public function __construct(?string $startDate = null, ?string $endDate = null)
    {
        $this->startDate = $startDate;
        $this->endDate = $endDate;
    }

    public function getStartDate(): ?string
    {
        return $this->startDate;
    }

    public function setStartDate(?string $d): self
    {
        $this->startDate = $d;
        return $this;
    }

    public function getEndDate(): ?string
    {
        return $this->endDate;
    }

    public function setEndDate(?string $d): self
    {
        $this->endDate = $d;
        return $this;
    }

    public static function xmlDeserialize(Reader $reader)
    {
        $keyValues = \Sabre\Xml\Deserializer\keyValue($reader);

        return (new static())
            ->setStartDate($keyValues[Schema::CBC . 'StartDate'] ?? null)
            ->setEndDate($keyValues[Schema::CBC . 'EndDate'] ?? null)
        ;
    }

    public function xmlSerialize(Writer $writer): void
    {
        $writer->write([
            Schema::CBC . 'StartDate' => $this->startDate,
            Schema::CBC . 'EndDate' => $this->endDate,
        ]);
    }
}
