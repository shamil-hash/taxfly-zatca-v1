<?php

namespace App\Helpers;

use DateTime;
use DateTimeZone;
use Illuminate\Support\Facades\Log;

class ZatcaHelper
{
    /**
     * Generates a ZATCA-compliant QR code (TLV format) using HEXADECIMAL encoding
     * and returns it as a base64-encoded string.
     */
    public static function generateQrCode(
        string $sellerName,
        string $vatNumber,
        string $timestampISO8601,
        string $totalWithVat,
        string $vatTotal
    ): string {
        try {
            // Validate and format inputs
            $sellerName = substr(trim($sellerName), 0, 100);
            $vatNumber = preg_replace('/[^0-9]/', '', $vatNumber);
            
            // Ensure VAT number is exactly 15 digits (ZATCA requirement)
            if (strlen($vatNumber) !== 15) {
                Log::channel('zatca')->warning('VAT number must be 15 digits, padding/truncating', [
                    'original' => $vatNumber,
                    'length' => strlen($vatNumber)
                ]);
                $vatNumber = str_pad(substr($vatNumber, 0, 15), 15, '0', STR_PAD_LEFT);
            }

            // Ensure timestamp is in proper Zulu format
            $dateTime = new DateTime($timestampISO8601);
            if ($dateTime->getTimezone()->getName() !== 'UTC') {
                $dateTime->setTimezone(new DateTimeZone('UTC'));
            }
            $timestamp = $dateTime->format('Y-m-d\TH:i:s\Z');

            // Format amounts to exactly 2 decimal places
            $totalWithVatFormatted = number_format((float)$totalWithVat, 2, '.', '');
            $vatTotalFormatted = number_format((float)$vatTotal, 2, '.', '');

            // ZATCA requires HEXADECIMAL TLV encoding
            $tlvData = [
                // Tag 1: Seller's name (max 100 chars)
                '01' => $sellerName,
                // Tag 2: VAT registration number (exactly 15 digits)
                '02' => $vatNumber,
                // Tag 3: Time stamp in Zulu time
                '03' => $timestamp,
                // Tag 4: Invoice total with VAT
                '04' => $totalWithVatFormatted,
                // Tag 5: VAT total
                '05' => $vatTotalFormatted
            ];

            // Build TLV string using HEXADECIMAL format
            $tlvString = '';
            foreach ($tlvData as $tag => $value) {
                $length = strlen($value);
                // Convert length to 2-digit hex
                $hexLength = str_pad(dechex($length), 2, '0', STR_PAD_LEFT);
                $tlvString .= $tag . $hexLength . $value;
            }

            Log::channel('zatca')->debug('Generated ZATCA TLV HEX string', [
                'tlv_string' => $tlvString,
                'length' => strlen($tlvString),
                'preview' => substr($tlvString, 0, 50) . '...'
            ]);

            return base64_encode($tlvString);

        } catch (\Exception $e) {
            Log::channel('zatca')->error('ZATCA QR Code generation failed: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            return base64_encode('');
        }
    }

    /**
     * Alternative method using DateTime object
     */
    public static function generateQrCodeOfficial(
        string $sellerName,
        string $vatNumber,
        DateTime $invoiceDate,
        float $totalWithVat,
        float $vatTotal
    ): string {
        try {
            if ($invoiceDate->getTimezone()->getName() !== 'UTC') {
                $invoiceDate->setTimezone(new DateTimeZone('UTC'));
            }
            $timestampISO8601 = $invoiceDate->format('Y-m-d\TH:i:s\Z');

            return self::generateQrCode(
                $sellerName,
                $vatNumber,
                $timestampISO8601,
                number_format($totalWithVat, 2, '.', ''),
                number_format($vatTotal, 2, '.', '')
            );

        } catch (\Exception $e) {
            Log::channel('zatca')->error('ZATCA QR Code generation (Official) failed: ' . $e->getMessage());
            return base64_encode('');
        }
    }

    /**
     * Validates the ZATCA TLV format
     */
    public static function validateQrCode(string $base64TlvString): bool
    {
        if (empty($base64TlvString)) {
            return false;
        }

        $decoded = base64_decode($base64TlvString, true);
        if ($decoded === false) {
            Log::channel('zatca')->warning('QR Validation: Invalid base64 encoding.');
            return false;
        }

        // Check for required tags in hexadecimal format
        $requiredTags = ['01', '02', '03', '04', '05'];
        $tlvString = $decoded;
        
        foreach ($requiredTags as $tag) {
            if (strpos($tlvString, $tag) === false) {
                Log::channel('zatca')->warning("QR Validation: Missing required tag $tag");
                return false;
            }
        }

        // Basic length check
        if (strlen($tlvString) < 30) {
            Log::channel('zatca')->warning('QR Validation: TLV string too short.');
            return false;
        }

        return true;
    }

    /**
     * Parses a ZATCA TLV string for debugging
     */
    public static function parseQrCode(string $base64TlvString): ?array
    {
        $decoded = base64_decode($base64TlvString, true);
        if ($decoded === false) {
            return null;
        }

        $tlvString = $decoded;
        $parsedData = [];
        $position = 0;
        $length = strlen($tlvString);

        while ($position < $length) {
            // Extract tag (2 hex digits)
            if ($position + 2 > $length) break;
            $tag = substr($tlvString, $position, 2);
            $position += 2;

            // Extract length (2 hex digits)
            if ($position + 2 > $length) break;
            $hexLength = substr($tlvString, $position, 2);
            $position += 2;

            // Convert hex length to decimal
            $valueLength = hexdec($hexLength);

            // Extract value
            if ($position + $valueLength > $length) break;
            $value = substr($tlvString, $position, $valueLength);
            $position += $valueLength;

            $parsedData[$tag] = $value;
        }

        return $parsedData;
    }

    /**
     * Simple method to check if QR code contains basic required data
     */
    public static function isQrCodeValid(string $base64TlvString): bool
    {
        $parsed = self::parseQrCode($base64TlvString);
        
        if (!$parsed) {
            return false;
        }

        // Check if all required tags are present
        $requiredTags = ['01', '02', '03', '04', '05'];
        foreach ($requiredTags as $tag) {
            if (!isset($parsed[$tag]) || empty($parsed[$tag])) {
                return false;
            }
        }

        return true;
    }

    /**
     * Debug method to display QR code content in human-readable format
     */
    public static function debugQrCode(string $base64TlvString): array
    {
        $parsed = self::parseQrCode($base64TlvString);
        
        if (!$parsed) {
            return ['error' => 'Invalid QR code format'];
        }

        $debugInfo = [];
        foreach ($parsed as $tag => $value) {
            switch ($tag) {
                case '01':
                    $debugInfo['seller_name'] = $value;
                    break;
                case '02':
                    $debugInfo['vat_number'] = $value;
                    $debugInfo['vat_number_valid'] = strlen($value) === 15;
                    break;
                case '03':
                    $debugInfo['timestamp'] = $value;
                    $debugInfo['timestamp_valid'] = preg_match('/^\d{4}-\d{2}-\d{2}T\d{2}:\d{2}:\d{2}Z$/', $value);
                    break;
                case '04':
                    $debugInfo['total_with_vat'] = $value;
                    $debugInfo['total_numeric'] = is_numeric($value);
                    break;
                case '05':
                    $debugInfo['vat_total'] = $value;
                    $debugInfo['vat_numeric'] = is_numeric($value);
                    break;
                default:
                    $debugInfo["unknown_tag_$tag"] = $value;
            }
        }

        return $debugInfo;
    }

    /**
     * Generates a sample QR code for testing
     */
    public static function generateSampleQrCode(): string
    {
        return self::generateQrCode(
            'Netplex Solutions',
            '300000000000003', // 15-digit VAT number
            '2024-01-01T12:00:00Z',
            '115.00',
            '15.00'
        );
    }

    /**
     * Validates all aspects of a ZATCA QR code
     */
    public static function validateZatcaQrCode(string $base64TlvString): array
    {
        $result = [
            'valid' => true,
            'errors' => [],
            'warnings' => []
        ];

        // Basic validation
        if (!self::validateQrCode($base64TlvString)) {
            $result['valid'] = false;
            $result['errors'][] = 'Basic TLV structure validation failed';
            return $result;
        }

        // Parse and detailed validation
        $parsed = self::parseQrCode($base64TlvString);
        if (!$parsed) {
            $result['valid'] = false;
            $result['errors'][] = 'Failed to parse TLV structure';
            return $result;
        }

        // Check required tags
        $requiredTags = ['01', '02', '03', '04', '05'];
        foreach ($requiredTags as $tag) {
            if (!isset($parsed[$tag])) {
                $result['valid'] = false;
                $result['errors'][] = "Missing required tag: $tag";
            }
        }

        // Validate VAT number format (15 digits)
        if (isset($parsed['02']) && strlen($parsed['02']) !== 15) {
            $result['valid'] = false;
            $result['errors'][] = 'VAT number must be exactly 15 digits';
        }

        // Validate timestamp format
        if (isset($parsed['03']) && !preg_match('/^\d{4}-\d{2}-\d{2}T\d{2}:\d{2}:\d{2}Z$/', $parsed['03'])) {
            $result['valid'] = false;
            $result['errors'][] = 'Invalid timestamp format, must be ISO 8601 Zulu time';
        }

        // Validate numeric amounts
        if (isset($parsed['04']) && !is_numeric($parsed['04'])) {
            $result['valid'] = false;
            $result['errors'][] = 'Total amount must be numeric';
        }

        if (isset($parsed['05']) && !is_numeric($parsed['05'])) {
            $result['valid'] = false;
            $result['errors'][] = 'VAT amount must be numeric';
        }

        return $result;
    }
}