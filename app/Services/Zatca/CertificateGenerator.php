<?php
namespace App\Services\Zatca;

use Saleh7\Zatca\CertificateBuilder;
use Saleh7\Zatca\Exceptions\CertificateBuilderException;

class CertificateGenerator
{
    public function generate()
    {
        // Ensure the dev directory exists
        $zatcaDevPath = storage_path('zatca/dev');
        if (!file_exists($zatcaDevPath)) {
            if (!mkdir($zatcaDevPath, 0755, true)) {
                return "Error: Failed to create directory: " . $zatcaDevPath;
            }
        }

        try {
            $builder = (new CertificateBuilder())
                ->setOrganizationIdentifier('312345678901233')
                ->setSerialNumber('Saleh', '1n', 'SME00023')
                ->setCommonName('Taxfly')
                ->setCountryName('SA')
                ->setOrganizationName('Netplex Solutions')
                ->setOrganizationalUnitName('IT Department')
                ->setAddress('Riyadh 1234 Street')
                ->setInvoiceType(1100)
                ->setProduction(false)
                ->setBusinessCategory('Software Development');

            // Generate and save CSR and private key
            $builder->generateAndSave(
                storage_path('zatca/dev/certificate.csr'), 
                storage_path('zatca/dev/private.pem')
            );

            // Verify files were created
            $csrPath = storage_path('zatca/dev/certificate.csr');
            $keyPath = storage_path('zatca/dev/private.pem');
            
            if (!file_exists($csrPath) || !file_exists($keyPath)) {
                return "Error: CSR or private key files were not created successfully.";
            }

            // Verify file contents
            $csrContent = file_get_contents($csrPath);
            $keyContent = file_get_contents($keyPath);
            
            if (empty($csrContent) || empty($keyContent)) {
                return "Error: Generated files are empty.";
            }

            return "CSR and private key generated successfully.\n" .
                   "CSR: " . $csrPath . "\n" .
                   "Private Key: " . $keyPath . "\n" .
                   "Next step: Run php artisan zatca:request-compliance [OTP]";

        } catch (CertificateBuilderException $e) {
            return "Certificate Builder Error: " . $e->getMessage();
        } catch (\Exception $e) {
            return "Unexpected Error: " . $e->getMessage();
        }
    }

    /**
     * Check if CSR and private key already exist
     */
    public function certificatesExist(): bool
    {
        $csrPath = storage_path('zatca/dev/certificate.csr');
        $keyPath = storage_path('zatca/dev/private.pem');
        
        return file_exists($csrPath) && file_exists($keyPath);
    }

    /**
     * Get the CSR content for display or submission
     */
    public function getCsrContent(): string
    {
        $csrPath = storage_path('zatca/dev/certificate.csr');
        
        if (!file_exists($csrPath)) {
            return "CSR file not found. Please generate it first.";
        }
        
        return file_get_contents($csrPath);
    }

    /**
     * Validate the generated CSR format
     */
    public function validateCsr(): array
    {
        $csrPath = storage_path('zatca/dev/certificate.csr');
        
        if (!file_exists($csrPath)) {
            return ['valid' => false, 'message' => 'CSR file not found'];
        }
        
        $csrContent = file_get_contents($csrPath);
        
        // Basic validation - check if it looks like a CSR
        $isValid = str_contains($csrContent, '-----BEGIN CERTIFICATE REQUEST-----') &&
                  str_contains($csrContent, '-----END CERTIFICATE REQUEST-----');
        
        return [
            'valid' => $isValid,
            'message' => $isValid ? 'CSR appears valid' : 'CSR format appears invalid'
        ];
    }
}