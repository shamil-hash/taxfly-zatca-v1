<?php
namespace App\Services\Zatca;

use Saleh7\Zatca\ZatcaAPI;
use Saleh7\Zatca\Exceptions\ZatcaApiException;

class CertificateRequester
{
    public function request(?string $otp = null) // Add ? to make it explicitly nullable
    {
        $client = new ZatcaAPI('sandbox');

        try {
            // For development, you can generate a mock compliance certificate
            // without actually calling ZATCA API
            if (app()->environment('local', 'development') && empty($otp)) {
                return $this->generateMockComplianceCertificate();
            }

            // If still no OTP provided, use the test OTP
            if (empty($otp)) {
                $otp = '123456'; // ZATCA sandbox test OTP
            }

            $csr = $client->loadCSRFromFile(storage_path('zatca/dev/certificate.csr'));
            $result = $client->requestComplianceCertificate($csr, $otp);

            $client->saveToJson(
                $result->getCertificate(),
                $result->getSecret(),
                $result->getRequestId(),
                storage_path('zatca/dev/ZATCA_certificate_data.json')
            );

            return "Compliance certificate saved successfully.";

        } catch (ZatcaApiException | \Exception $e) {
            return "Error: " . $e->getMessage();
        }
    }

    /**
     * Generate mock compliance certificate for development
     */
    private function generateMockComplianceCertificate(): string
    {
        $zatcaDevPath = storage_path('zatca/dev');
        if (!file_exists($zatcaDevPath)) {
            mkdir($zatcaDevPath, 0755, true);
        }

        // Read the CSR to get some details
        $csrPath = storage_path('zatca/dev/certificate.csr');
        
        if (!file_exists($csrPath)) {
            throw new \RuntimeException("CSR file not found. Please generate CSR first.");
        }
        
        $csrContent = file_get_contents($csrPath);
        
        // Generate mock certificate data
        $mockData = [
            'certificate' => "-----BEGIN CERTIFICATE-----\nMIIF...MOCK_CERTIFICATE...\n-----END CERTIFICATE-----",
            'secret' => 'mock-secret-key-12345',
            'requestId' => 'mock-request-id-' . time(),
            'generated_at' => now()->toISOString(),
            'environment' => 'sandbox',
            'is_mock' => true
        ];

        // Save mock certificate data
        file_put_contents(
            storage_path('zatca/dev/ZATCA_certificate_data.json'),
            json_encode($mockData, JSON_PRETTY_PRINT)
        );

        return "Mock compliance certificate generated for development.";
    }
}