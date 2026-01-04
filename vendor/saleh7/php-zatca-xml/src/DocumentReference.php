<?php
namespace Saleh7\Zatca;

use Sabre\Xml\Reader;
use Sabre\Xml\Writer;
use Sabre\Xml\XmlDeserializable;
use Sabre\Xml\XmlSerializable;

/**
 * Backwards-compatible alias for AdditionalDocumentReference.
 * Many parts of the library reference DocumentReference but the actual
 * implementation file is AdditionalDocumentReference.php. To avoid
 * undefined type errors, DocumentReference extends AdditionalDocumentReference.
 */
class DocumentReference extends AdditionalDocumentReference implements XmlSerializable, XmlDeserializable
{
    // Inherits behavior from AdditionalDocumentReference.
    public static function xmlDeserialize(Reader $reader)
    {
        return AdditionalDocumentReference::xmlDeserialize($reader);
    }

    public function xmlSerialize(Writer $writer): void
    {
        parent::xmlSerialize($writer);
    }
}
