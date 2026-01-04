<?php

namespace App\Services;

use RuntimeException;

class ZatcaQrGenerator
{
    /**
     * Create ZATCA TLV byte sequence then base64 encode it (for QR payload).
     *
     * Required fields order (ZATCA):
     * 1. Seller name (string)
     * 2. VAT registration number (string)
     * 3. Invoice total (string) - formatted decimal
     * 4. VAT total (string) - formatted decimal
     * 5. Invoice timestamp (ISO 8601) - e.g. 2024-01-01T12:00:00Z
     *
     * Returns raw binary TLV bytes (not base64) — caller can base64_encode() as needed.
     */
    public static function buildTlvBytes(array $fields): string
    {
        // fields: name, vat, total, vat_total, timestamp
        $tags = [
            1 => $fields['seller_name'] ?? '',
            2 => $fields['seller_tax_number'] ?? '',
            3 => $fields['invoice_total'] ?? '',
            4 => $fields['vat_total'] ?? '',
            5 => $fields['timestamp'] ?? '',
        ];

        $bytes = '';
        foreach ($tags as $tag => $value) {
            if ($value === null) $value = '';
            $valueBytes = mb_convert_encoding((string)$value, 'UTF-8');
            $len = strlen($valueBytes);
            $bytes .= chr($tag) . chr($len) . $valueBytes;
        }

        return $bytes;
    }

    /**
     * Generate QR image (PNG binary) for provided TLV bytes.
     * Uses Simple QrCode (simplesoftwareio/simple-qrcode) if available,
     * otherwise tries endroid/qr-code.
     *
     * Returns PNG binary string.
     */
    public static function generateQrPngFromTlv(string $tlvBytes, int $size = 300): string
    {
        $payload = base64_encode($tlvBytes);

        // Prefer Simple QrCode
        if (class_exists(\SimpleSoftwareIO\QrCode\Facades\QrCode::class) || class_exists(\SimpleSoftwareIO\QrCode\Generator::class)) {
            // If Facade not registered, use the generator directly via container
            if (defined('\SimpleSoftwareIO\QrCode\Facades\QrCode')) {
                // facade available
                return \SimpleSoftwareIO\QrCode\Facades\QrCode::format('png')->size($size)->generate($payload);
            } else {
                $qr = new \SimpleSoftwareIO\QrCode\Generator();
                return $qr->format('png')->size($size)->generate($payload);
            }
        }

        // Fallback: Endroid QR Code
        if (class_exists(\Endroid\QrCode\Builder\Builder::class)) {
            $result = \Endroid\QrCode\Builder\Builder::create()
                ->data($payload)
                ->size($size)
                ->build();
            return $result->getString(); // PNG binary
        }

        throw new RuntimeException('No QR library available. Install simplesoftwareio/simple-qrcode or endroid/qr-code.');
    }

    /**
     * Convenience: return data URI for embedding in <img src="...">
     */
    public static function generateQrDataUriFromTlv(string $tlvBytes, int $size = 300): string
    {
        $png = static::generateQrPngFromTlv($tlvBytes, $size);
        return 'data:image/png;base64,' . base64_encode($png);
    }
}
