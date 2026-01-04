<?php

namespace App\Services\Zatca;

use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\PngWriter;
use Endroid\QrCode\ErrorCorrectionLevel\ErrorCorrectionLevelHigh;
use Endroid\QrCode\Color\Color;
use Saleh7\Zatca\Helpers\Certificate;
use Illuminate\Support\Facades\Log;
use Tecfinite\ZatcaQr\ZatcaQr as TecfiniteZatcaQr;
use Tecfinite\ZatcaQr\Tag\SellerTag as TecfiniteSellerTag;
use Tecfinite\ZatcaQr\Tag\VatRegistrationNumberTag as TecfiniteVatRegistrationNumberTag;
use Tecfinite\ZatcaQr\Tag\TimestampTag as TecfiniteTimestampTag;
use Tecfinite\ZatcaQr\Tag\InvoiceTotalTag as TecfiniteInvoiceTotalTag;
use Tecfinite\ZatcaQr\Tag\VatTotalTag as TecfiniteVatTotalTag;

class InvoiceQrGenerator
{
    /**
     * Generate TLV QR code (Base64 string).
     */
    public function generateBase64(?string $signedInvoiceXmlPath, ?Certificate $certificate, array $invoiceData = []): string
    {
        try {
            Log::channel('zatca')->debug('Generating QR code', [
                'has_signed_xml' => !empty($signedInvoiceXmlPath),
                'has_certificate' => !empty($certificate),
                'invoice_number' => $invoiceData['invoice_number'] ?? 'unknown'
            ]);

            // Use Tecfinite library for consistent TLV generation
            $tecfiniteQr = $this->generateWithTecfiniteLibrary($invoiceData);
            
            if (!empty($tecfiniteQr)) {
                Log::channel('zatca')->debug('QR generated from Tecfinite library successfully');
                return $tecfiniteQr;
            }

            // Fallback to manual TLV
            Log::channel('zatca')->warning('Tecfinite QR generation failed, falling back to manual TLV');
            return $this->generateManualTlvQr($invoiceData);

        } catch (\Throwable $e) {
            Log::channel('zatca')->error('QR generation failed completely', [
                'error' => $e->getMessage(),
                'invoice_number' => $invoiceData['invoice_number'] ?? 'unknown'
            ]);
            return '';
        }
    }

    /**
     * Generate QR using Tecfinite library
     */
    protected function generateWithTecfiniteLibrary(array $invoiceData): string
    {
        try {
            $supplierName   = trim($invoiceData['supplier_name'] ?? 'Unknown Supplier');
            $supplierId     = trim($invoiceData['supplier_id'] ?? '0000000000');

            $issueDateTime = $invoiceData['issue_datetime'] ?? now();
            if ($issueDateTime instanceof \DateTimeInterface) {
                $invoiceDate = $issueDateTime->format('Y-m-d\TH:i:s\Z');
            } else {
                $invoiceDate = (string) $issueDateTime;
                if (!preg_match('/Z$/', $invoiceDate)) {
                    $invoiceDate .= 'Z';
                }
            }

            $totalAmount    = number_format((float)($invoiceData['total_amount'] ?? 0), 2, '.', '');
            $vatAmount      = number_format((float)($invoiceData['vat_amount'] ?? 0), 2, '.', '');

            // Use Tecfinite library
            $zatcaQr = new TecfiniteZatcaQr(
                new TecfiniteSellerTag($supplierName),
                new TecfiniteVatRegistrationNumberTag($supplierId),
                new TecfiniteTimestampTag($invoiceDate),
                new TecfiniteInvoiceTotalTag($totalAmount),
                new TecfiniteVatTotalTag($vatAmount)
            );

            $base64Tlv = $zatcaQr->toBase64();

            if (empty($base64Tlv)) {
                throw new \Exception('Tecfinite library returned empty TLV');
            }

            return $this->generateQrCodeImageFromBase64($base64Tlv);

        } catch (\Throwable $e) {
            Log::channel('zatca')->error('Tecfinite library QR generation failed', [
                'error' => $e->getMessage(),
                'invoice_data' => array_diff_key($invoiceData, array_flip(['line_items']))
            ]);
            throw $e;
        }
    }

    /**
     * Fallback manual TLV generation
     */
    protected function generateManualTlvQr(array $invoiceData): string
    {
        try {
            $supplierName   = trim($invoiceData['supplier_name'] ?? 'Unknown Supplier');
            $supplierId     = trim($invoiceData['supplier_id'] ?? '0000000000');
            
            $issueDateTime = $invoiceData['issue_datetime'] ?? now();
            if ($issueDateTime instanceof \DateTimeInterface) {
                $invoiceDate = $issueDateTime->format('Y-m-d\TH:i:s\Z');
            } else {
                $invoiceDate = (string) $issueDateTime;
                if (!preg_match('/Z$/', $invoiceDate)) {
                    $invoiceDate .= 'Z';
                }
            }
            
            $totalAmount    = number_format((float)($invoiceData['total_amount'] ?? 0), 2, '.', '');
            $vatAmount      = number_format((float)($invoiceData['vat_amount'] ?? 0), 2, '.', '');

            $tags = [
                new \Saleh7\Zatca\Tag(1, $supplierName),
                new \Saleh7\Zatca\Tag(2, $supplierId),
                new \Saleh7\Zatca\Tag(3, $invoiceDate),
                new \Saleh7\Zatca\Tag(4, $totalAmount),
                new \Saleh7\Zatca\Tag(5, $vatAmount),
            ];

            $qrGenerator = \Saleh7\Zatca\Helpers\QRCodeGenerator::createFromTags($tags);
            if (!$qrGenerator) {
                Log::channel('zatca')->error('Failed to create TLV QR generator');
                return '';
            }

            $base64 = $qrGenerator->encodeBase64();
            return $base64;

        } catch (\Throwable $e) {
            Log::channel('zatca')->error('Manual TLV QR generation failed', [
                'error' => $e->getMessage()
            ]);
            return '';
        }
    }

    /**
     * Save QR code as PNG file.
     */
    public function saveQrCode(string $qrBase64, string $filePath): void
    {
        try {
            if (empty($qrBase64)) {
                Log::channel('zatca')->warning('QR save skipped - empty base64 provided');
                return;
            }

            $dir = dirname($filePath);
            if (!is_dir($dir)) {
                mkdir($dir, 0755, true);
            }

            file_put_contents($filePath, base64_decode($qrBase64));
            Log::channel('zatca')->debug('QR code saved to file', ['file' => $filePath]);
        } catch (\Throwable $e) {
            Log::channel('zatca')->error('Failed to save QR code', [
                'file'  => $filePath,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Generate TLV for XML embedding using Tecfinite library
     */
    public function getTlvForXmlEmbedding(array $invoiceData): string
    {
        try {
            $supplierName   = trim($invoiceData['supplier_name'] ?? '');
            $supplierId     = trim($invoiceData['supplier_id'] ?? '');
            
            $issueDateTime = $invoiceData['issue_datetime'] ?? now();
            if ($issueDateTime instanceof \DateTimeInterface) {
                $invoiceDate = $issueDateTime->format('Y-m-d\TH:i:s\Z');
            } else {
                $invoiceDate = (string) $issueDateTime;
                if (!preg_match('/Z$/', $invoiceDate)) {
                    $invoiceDate .= 'Z';
                }
            }

            $totalAmount    = number_format((float)($invoiceData['total_amount'] ?? 0), 2, '.', '');
            $vatAmount      = number_format((float)($invoiceData['vat_amount'] ?? 0), 2, '.', '');

            // Use Tecfinite library
            $zatcaQr = new TecfiniteZatcaQr(
                new TecfiniteSellerTag($supplierName),
                new TecfiniteVatRegistrationNumberTag($supplierId),
                new TecfiniteTimestampTag($invoiceDate),
                new TecfiniteInvoiceTotalTag($totalAmount),
                new TecfiniteVatTotalTag($vatAmount)
            );

            return $zatcaQr->toBase64();

        } catch (\Throwable $e) {
            Log::channel('zatca')->error('TLV generation for XML failed', [
                'error' => $e->getMessage()
            ]);
            return '';
        }
    }

    /**
     * ZATCA Phase 2 compliant QR generation with cryptographic tags
     */
    public function generateBase64WithCrypto(string $signedXmlContent, $certificate, array $invoiceData): string
    {
        Log::channel('zatca')->info('Generating ZATCA-compliant QR code with cryptographic tags', [
            'certificate_type' => get_class($certificate)
        ]);
        
        try {
            // Check certificate compliance
            $certificateCheck = $this->checkCertificateCompliance($certificate);
            if (!$certificateCheck['zatca_compliant']) {
                throw new \Exception("Certificate not ZATCA compliant: " . $certificateCheck['message']);
            }

            $invoiceData['supplier_vat'] = trim(mb_convert_encoding($invoiceData['supplier_vat'] ?? '', 'UTF-8'));
            $invoiceData['customer_vat'] = trim(mb_convert_encoding($invoiceData['customer_vat'] ?? '', 'UTF-8'));
            $invoiceData['supplier_address'] = trim(mb_convert_encoding($invoiceData['supplier_address'] ?? '', 'UTF-8'));
            $invoiceData['customer_address'] = trim(mb_convert_encoding($invoiceData['customer_address'] ?? '', 'UTF-8'));

            // Generate basic tags (1-5)
            $basicTlv = $this->generateBasicTlv($invoiceData);
            
            // Cryptographic tags (6-9) - raw binary
            $invoiceHash = hash('sha256', $signedXmlContent, true); // Tag 6
            
            $signature = $this->generateEcdsaSignatureZatca($invoiceHash, $certificate, $returnBinary = true); // Tag 7
            
            $publicKey = $this->extractPublicKeyZatca($certificate);
            $publicKey = $this->pemToDer($publicKey); // Tag 8
            
            $previousInvoiceHash = $invoiceData['previous_invoice_hash'] ?? null;
            if (!empty($previousInvoiceHash)) {
                if (strlen($previousInvoiceHash) === 44) { // Base64
                    $previousInvoiceHash = base64_decode($previousInvoiceHash);
                } elseif (ctype_xdigit($previousInvoiceHash) && strlen($previousInvoiceHash) === 64) {
                    $previousInvoiceHash = hex2bin($previousInvoiceHash);
                }
                if (strlen($previousInvoiceHash) !== 32) {
                    throw new \Exception("Previous invoice hash must be 32 bytes");
                }
            } else {
                $previousInvoiceHash = str_repeat("\x00", 32);
            }

            $completeTlv = $basicTlv
                . $this->createTlvTag(6, $invoiceHash)
                . $this->createTlvTag(7, $signature)
                . $this->createTlvTag(8, $publicKey)
                . $this->createTlvTag(9, $previousInvoiceHash);

            // Debug TLV structure
            $this->debugTlvStructure($completeTlv);
            
            Log::channel('zatca')->debug('ZATCA-compliant TLV generated', [
                'total_length' => strlen($completeTlv),
                'tags_present' => [1, 2, 3, 4, 5, 6, 7, 8, 9]
            ]);
            
            // Convert to Base64 for QR generation
            $base64Tlv = base64_encode($completeTlv);
            // ✅ NEW — Returns TLV Base64 string — CORRECT for XML embedding
            return $base64Tlv;
            
        } catch (\Throwable $e) {
            Log::channel('zatca')->error('ZATCA QR generation failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            throw new \Exception("ZATCA QR generation failed: " . $e->getMessage());
        }
    }

    /**
     * Check if certificate is ZATCA compliant (ECDSA P-256)
     */
    private function checkCertificateCompliance($certificate): array
    {
        try {
            if ($certificate instanceof \Saleh7\Zatca\Helpers\Certificate) {
                $privateKey = $certificate->getPrivateKey();
                
                $isEcdsa = $privateKey instanceof \phpseclib3\Crypt\EC\PrivateKey;
                $isRsa = $privateKey instanceof \phpseclib3\Crypt\RSA\PrivateKey;
                
                return [
                    'zatca_compliant' => $isEcdsa,
                    'certificate_type' => $isEcdsa ? 'ECDSA P-256' : ($isRsa ? 'RSA' : 'Unknown'),
                    'message' => $isEcdsa ? 'Certificate is ZATCA compliant' : 'ZATCA requires ECDSA P-256 certificate'
                ];
            }
            
            return [
                'zatca_compliant' => false,
                'certificate_type' => 'Unknown',
                'message' => 'Cannot determine certificate type'
            ];
            
        } catch (\Throwable $e) {
            return [
                'zatca_compliant' => false,
                'certificate_type' => 'Error',
                'message' => 'Certificate check failed: ' . $e->getMessage()
            ];
        }
    }
    
    private function pemToDer(string $pem): string
    {
        $pem = trim(str_replace(["-----BEGIN PUBLIC KEY-----", "-----END PUBLIC KEY-----", "\r", "\n"], '', $pem));
        return base64_decode($pem);
    }

    /**
     * Generate basic TLV tags (1-5)
     */
    private function generateBasicTlv(array $invoiceData): string
    {
        $tlv = '';
        
        // Tag 1: Seller name
        $sellerName = $invoiceData['supplier_name'] ?? '';
        $tlv .= $this->createTlvTag(1, $sellerName);
        
        // Tag 2: VAT registration number
        $vatNumber = $invoiceData['supplier_id'] ?? '';
        $tlv .= $this->createTlvTag(2, $vatNumber);
        
        // Tag 3: Timestamp (ISO 8601 with Z suffix)
        $timestamp = $invoiceData['issue_datetime'] ?? '';
        if ($timestamp instanceof \DateTimeInterface) {
            $timestamp = $timestamp->format('Y-m-d\TH:i:s\Z');
        } elseif (is_string($timestamp)) {
            $dateTime = new \DateTime($timestamp);
            $timestamp = $dateTime->format('Y-m-d\TH:i:s\Z');
        } else {
            $timestamp = (new \DateTime())->format('Y-m-d\TH:i:s\Z');
        }
        $tlv .= $this->createTlvTag(3, $timestamp);
        
        // Tag 4: Invoice total with VAT
        $invoiceTotal = number_format($invoiceData['total_amount'] ?? 0, 2, '.', '');
        $tlv .= $this->createTlvTag(4, $invoiceTotal);
        
        // Tag 5: VAT total amount
        $vatTotal = number_format($invoiceData['vat_amount'] ?? 0, 2, '.', '');
        $tlv .= $this->createTlvTag(5, $vatTotal);
        
        return $tlv;
    }

    /**
     * Create a TLV tag
     */
    private function createTlvTag(int $tag, string $value): string
    {
        $length = strlen($value);
        return pack('C', $tag) . pack('C', $length) . $value;
    }

    /**
     * ZATCA-compliant ECDSA signature (raw binary)
     */
    private function generateEcdsaSignatureZatca(string $data, $certificate): string
    {
        try {
            if ($certificate instanceof \Saleh7\Zatca\Helpers\Certificate) {
                $privateKey = $certificate->getPrivateKey();
                $signature = $privateKey->sign($data);
                
                Log::channel('zatca')->debug('ECDSA signature generated', [
                    'data_length' => strlen($data),
                    'signature_length' => strlen($signature),
                    'is_binary' => true
                ]);
                
                return $signature;
            }
            
            throw new \Exception("Unsupported certificate type for ZATCA signing");
            
        } catch (\Throwable $e) {
            Log::channel('zatca')->error('ECDSA signature generation failed', ['error' => $e->getMessage()]);
            throw new \Exception("ECDSA signature failed: " . $e->getMessage());
        }
    }

    /**
     * ZATCA-compliant public key extraction (raw binary)
     */
    private function extractPublicKeyZatca($certificate): string
    {
        try {
            if ($certificate instanceof \Saleh7\Zatca\Helpers\Certificate) {
                $publicKey = $certificate->getRawPublicKey();
                
                Log::channel('zatca')->debug('Public key extracted', [
                    'public_key_length' => strlen($publicKey),
                    'is_binary' => true
                ]);
                
                return $publicKey;
            }
            
            throw new \Exception("Unsupported certificate type for public key extraction");
            
        } catch (\Throwable $e) {
            Log::channel('zatca')->error('Public key extraction failed', ['error' => $e->getMessage()]);
            throw new \Exception("Public key extraction failed: " . $e->getMessage());
        }
    }

    /**
     * Generate QR code image from Base64 TLV (UTF-8 safe)
     */
    public function generateQrCodeImageFromBase64(string $base64Tlv): string
    {
        try {
            $qrCode = QrCode::create($base64Tlv)
                ->setSize(300)
                ->setMargin(10)
                ->setEncoding(new \Endroid\QrCode\Encoding\Encoding('UTF-8'));
            
            $writer = new PngWriter();
            $result = $writer->write($qrCode);
            
            return base64_encode($result->getString());
            
        } catch (\Throwable $e) {
            Log::channel('zatca')->error('QR code image generation failed', ['error' => $e->getMessage()]);
            throw new \Exception("QR code image generation failed: " . $e->getMessage());
        }
    }

    /**
     * Debug TLV structure for detailed analysis
     */
    private function debugTlvStructure(string $completeTlv): void
    {
        $tlvDebug = [];
        $offset = 0;
        
        while ($offset < strlen($completeTlv)) {
            $tag = ord($completeTlv[$offset++]);
            $length = ord($completeTlv[$offset++]);
            $value = substr($completeTlv, $offset, $length);
            $offset += $length;
            
            $valuePreview = substr($value, 0, 30);
            if (strlen($value) > 30) {
                $valuePreview .= '...[' . (strlen($value) - 30) . ' more bytes]';
            }
            
            $tlvDebug[] = [
                'tag' => $tag,
                'length' => $length,
                'value_preview' => $valuePreview,
                'is_binary' => base64_encode($value) !== $value
            ];
        }
        
        Log::channel('zatca')->debug('TLV structure analysis', ['tags' => $tlvDebug]);
    }

    /**
     * Get the actual TLV content for XML embedding (Base64 encoded)
     */
    public function getZatcaTlvContent(array $invoiceData, string $signedXmlContent, $certificate): string
    {
        try {
            // Generate basic tags (1-5)
            $basicTlv = $this->generateBasicTlv($invoiceData);
            
            // Cryptographic tags (6-9)
            $invoiceHash = hash('sha256', $signedXmlContent, true);
            $signature = $this->generateEcdsaSignatureZatca($invoiceHash, $certificate);
            $publicKey = $this->extractPublicKeyZatca($certificate);
            $zatcaSignature = str_repeat("\x00", 32);
            
            // Combine all tags
            $completeTlv = $basicTlv . 
                $this->createTlvTag(6, $invoiceHash) .
                $this->createTlvTag(7, $signature) .
                $this->createTlvTag(8, $publicKey) .
                $this->createTlvTag(9, $zatcaSignature);

            // Return Base64 encoded TLV for XML embedding
            return base64_encode($completeTlv);
            
        } catch (\Throwable $e) {
            Log::channel('zatca')->error('Failed to generate TLV content for XML', [
                'error' => $e->getMessage()
            ]);
            return '';
        }
    }

    /**
     * Simple QR generation without cryptographic tags (for basic testing)
     */
    public function generateSimpleQr(array $invoiceData): string
    {
        return $this->generateWithTecfiniteLibrary($invoiceData);
    }

    /**
     * Validate that QR generation is working
     */
    public function validateQrGeneration(): array
    {
        $testData = [
            'supplier_name' => 'Test Supplier',
            'supplier_id' => '300000000000003',
            'issue_datetime' => now(),
            'total_amount' => 100.00,
            'vat_amount' => 15.00,
            'invoice_number' => 'TEST001'
        ];

        try {
            $qrBase64 = $this->generateWithTecfiniteLibrary($testData);
            
            return [
                'success' => !empty($qrBase64),
                'qr_length' => strlen($qrBase64),
                'message' => !empty($qrBase64) ? 'QR generation working correctly' : 'QR generation failed'
            ];
            
        } catch (\Throwable $e) {
            return [
                'success' => false,
                'error' => $e->getMessage(),
                'message' => 'QR generation validation failed'
            ];
        }
    }
}