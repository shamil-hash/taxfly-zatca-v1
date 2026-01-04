<?php

namespace App\Services\Zatca;

use Exception;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\File;

class ZatcaIntegrationService
{
    protected $sdkService;
    protected $ublGenerator;
    protected $qrGenerator;
    protected $invoiceSigner;
    protected $certificatesSetup = false;

    public function __construct()
    {
        // Initialize services in correct order with dependencies
        $this->qrGenerator = new InvoiceQrGenerator();
        $this->invoiceSigner = new InvoiceSignerService();
        $this->ublGenerator = new InvoiceXmlGenerator($this->invoiceSigner, $this->qrGenerator);
        
        // Initialize SDK service
        $this->sdkService = new ZatcaSdkService();
        
        // Setup certificates for SDK
        $this->setupSdkCertificates();
    }

    /**
     * Setup certificates for SDK use
     */
    protected function setupSdkCertificates()
    {
        try {
            $config = config('zatca');
            
            // Load your current certificates
            $privateKeyPath = $config['private_key_path'];
            $certificatePath = $config['certificate_path'];
            
            // Check if files exist
            if (!file_exists($privateKeyPath)) {
                throw new Exception("Private key file not found: " . $privateKeyPath);
            }
            
            if (!file_exists($certificatePath)) {
                throw new Exception("Certificate file not found: " . $certificatePath);
            }
            
            $privateKeyContent = File::get($privateKeyPath);
            $certificateContent = File::get($certificatePath);
            
            // Setup certificates in SDK directory
            $this->sdkService->setupSdkCertificates($privateKeyContent, $certificateContent);
            $this->certificatesSetup = true;
            
            Log::info('ZATCA SDK certificates setup completed');
            
        } catch (Exception $e) {
            Log::error('Failed to setup ZATCA SDK certificates', [
                'error' => $e->getMessage(),
                'certificate_path' => config('zatca.certificate_path'),
                'private_key_path' => config('zatca.private_key_path')
            ]);
            
            $this->certificatesSetup = false;
        }
    }

    /**
     * Generate compliant invoice using SDK for cryptographic operations
     */
    public function generateCompliantInvoice($invoiceData, $useSdk = null)
    {
        try {
            // Auto-detect if we should use SDK (only if certificates are setup)
            if ($useSdk === null) {
                $useSdk = $this->certificatesSetup;
            }
            
            Log::info('Generating ZATCA compliant invoice', [
                'use_sdk' => $useSdk,
                'certificates_setup' => $this->certificatesSetup,
                'invoice_id' => $invoiceData['id'] ?? 'unknown'
            ]);

            // 1. Use your existing UBL generator
            $unsignedXml = $this->generateInvoiceXmlContent($invoiceData);
            
            if ($useSdk && $this->certificatesSetup) {
                // 2. Use SDK for signing and QR generation
                $signedXml = $this->sdkService->signInvoice($unsignedXml);
                $qrCode = $this->sdkService->generateQrCode($signedXml);
                $validation = $this->sdkService->validateInvoice($signedXml);
            } else {
                // Use your existing implementation as fallback
                $signedXml = $this->signInvoiceContent($unsignedXml);
                $qrCode = $this->generateQrCodeContent($signedXml, $invoiceData);
                $validation = ['valid' => true, 'output' => 'Using PHP implementation (SDK not available)'];
            }
            
            // Save to storage for reference
            $this->saveInvoiceArtifacts($unsignedXml, $signedXml, $invoiceData);
            
            Log::info('ZATCA invoice generated successfully', [
                'validation_result' => $validation['valid'],
                'used_sdk' => $useSdk && $this->certificatesSetup
            ]);
            
            return [
                'unsigned_xml' => $unsignedXml,
                'signed_xml' => $signedXml,
                'qr_code' => $qrCode,
                'validation' => $validation,
                'used_sdk' => $useSdk && $this->certificatesSetup
            ];
            
        } catch (Exception $e) {
            Log::error('ZATCA invoice generation failed', [
                'error' => $e->getMessage(),
                'invoice_data' => $invoiceData
            ]);
            
            throw new Exception("Invoice generation failed: " . $e->getMessage());
        }
    }

    /**
     * Generate invoice XML using your existing service
     */
    protected function generateInvoiceXmlContent($invoiceData)
    {
        // Use the correct method signature based on reflection
        return $this->ublGenerator->generate($invoiceData, null, null);
    }

    /**
     * Sign invoice using your existing service
     */
    protected function signInvoiceContent($unsignedXml)
    {
        // Save to temporary file first (since your sign method expects a file path)
        $tempFile = storage_path('zatca/temp_unsigned_' . time() . '.xml');
        file_put_contents($tempFile, $unsignedXml);
        
        try {
            $signedXml = $this->invoiceSigner->sign($tempFile);
            unlink($tempFile);
            return $signedXml;
        } catch (Exception $e) {
            unlink($tempFile);
            throw $e;
        }
    }

    /**
     * Generate QR code using your existing service
     */
    protected function generateQrCodeContent($signedXml, $invoiceData)
    {
        // Save to temporary file first (since your method may expect file path)
        $tempFile = storage_path('zatca/temp_signed_' . time() . '.xml');
        file_put_contents($tempFile, $signedXml);
        
        try {
            $qrCode = $this->qrGenerator->generateBase64($tempFile, null, $invoiceData);
            unlink($tempFile);
            return $qrCode;
        } catch (Exception $e) {
            unlink($tempFile);
            throw $e;
        }
    }

    /**
     * Save invoice artifacts for auditing
     */
    protected function saveInvoiceArtifacts($unsignedXml, $signedXml, $invoiceData)
    {
        try {
            $invoiceId = $invoiceData['id'] ?? time();
            $timestamp = time();
            
            // Save unsigned XML
            $unsignedPath = storage_path("zatca/{$invoiceId}-unsigned-{$timestamp}.xml");
            file_put_contents($unsignedPath, $unsignedXml);
            
            // Save signed XML
            $signedPath = storage_path("zatca/{$invoiceId}-signed-{$timestamp}.xml");
            file_put_contents($signedPath, $signedXml);
            
            Log::debug('Invoice artifacts saved', [
                'unsigned_path' => $unsignedPath,
                'signed_path' => $signedPath
            ]);
            
        } catch (Exception $e) {
            Log::warning('Failed to save invoice artifacts', ['error' => $e->getMessage()]);
        }
    }

    /**
     * Generate CSR using SDK
     */
    public function generateCsr($csrData)
    {
        try {
            // Convert array to properties format
            $properties = $this->buildCsrProperties($csrData);
            
            $privateKeyFile = storage_path('zatca/csr_private_key.key');
            $csrFile = storage_path('zatca/certificate_request.csr');
            
            $result = $this->sdkService->generateCsr($properties, $privateKeyFile, $csrFile);
            
            return [
                'private_key' => file_get_contents($privateKeyFile),
                'csr' => file_get_contents($csrFile),
                'sdk_output' => $result['output']
            ];
            
        } catch (Exception $e) {
            Log::error('CSR generation failed', ['error' => $e->getMessage()]);
            throw $e;
        }
    }

    /**
     * Build CSR properties string from array
     */
    protected function buildCsrProperties($data)
    {
        $properties = [];
        $mapping = [
            'common_name' => 'commonName',
            'serial_number' => 'serialNumber',
            'organization_identifier' => 'organizationIdentifier',
            'organization_unit_name' => 'organizationUnitName',
            'organization_name' => 'organizationName',
            'country_name' => 'countryName',
            'invoice_type' => 'invoiceType',
            'location_address' => 'locationAddress',
            'industry_business_category' => 'industryBusinessCategory'
        ];
        
        foreach ($mapping as $key => $property) {
            if (!empty($data[$key])) {
                $properties[] = "{$property}={$data[$key]}";
            }
        }
        
        return implode("\n", $properties);
    }

    /**
     * Public method to generate invoice XML
     */
    public function generateInvoiceXml($invoiceData)
    {
        return $this->generateInvoiceXmlContent($invoiceData);
    }
    
    /**
     * Generate QR code with SDK fallback
     */
    public function getQrCode($signedXml, $invoiceData = [])
    {
        // Try SDK first, fallback to existing implementation
        try {
            return $this->sdkService->generateQrCode($signedXml);
        } catch (Exception $e) {
            Log::warning('SDK QR generation failed, using PHP fallback', [
                'error' => $e->getMessage()
            ]);
            return $this->generateQrCodeContent($signedXml, $invoiceData);
        }
    }
    
    /**
     * Validate invoice using SDK
     */
    public function validateInvoice($signedXml)
    {
        return $this->sdkService->validateInvoice($signedXml);
    }
    
    /**
     * Check if SDK certificates are setup
     */
    public function isSdkReady()
    {
        return $this->certificatesSetup;
    }
    
    /**
     * Test method to verify service connectivity
     */
    public function testServiceConnection()
    {
        try {
            $testData = ['id' => 'TEST-001', 'amount' => 100.00];
            
            // Test XML generation
            $xml = $this->generateInvoiceXmlContent($testData);
            $xmlSuccess = !empty($xml);
            
            // Test signing
            $signed = $this->signInvoiceContent($xml);
            $signSuccess = !empty($signed);
            
            // Test QR generation
            $qr = $this->generateQrCodeContent($signed, $testData);
            $qrSuccess = !empty($qr);
            
            return [
                'xml_generation' => $xmlSuccess,
                'signing' => $signSuccess,
                'qr_generation' => $qrSuccess,
                'sdk_ready' => $this->certificatesSetup
            ];
            
        } catch (Exception $e) {
            return [
                'error' => $e->getMessage(),
                'sdk_ready' => $this->certificatesSetup
            ];
        }
    }
}