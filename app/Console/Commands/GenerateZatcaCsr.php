<?php
// app/Console/Commands/GenerateZatcaCsr.php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Saleh7\Zatca\CertificateBuilder;
use Saleh7\Zatca\Exceptions\CertificateBuilderException;

class GenerateZatcaCsr extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'zatca:generate-csr';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate ZATCA CSR and private key for compliance';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting ZATCA CSR generation...');
        
        // Ensure directory exists
        $zatcaDevPath = storage_path('zatca/dev');
        if (!file_exists($zatcaDevPath)) {
            mkdir($zatcaDevPath, 0755, true);
            $this->info('Created directory: ' . $zatcaDevPath);
        }

        try {
            (new CertificateBuilder())
                ->setOrganizationIdentifier('312345678901233')
                ->setSerialNumber('Saleh', '1n', 'SME00023')
                ->setCommonName('Taxfly')
                ->setCountryName('SA')
                ->setOrganizationName('Netplex Solutions')
                ->setOrganizationalUnitName('IT Department')
                ->setAddress('Riyadh 1234 Street')
                ->setInvoiceType(1100)
                ->setProduction(false)
                ->setBusinessCategory('Software Development')
                ->generateAndSave(
                    storage_path('zatca/dev/certificate.csr'),
                    storage_path('zatca/dev/private.pem')
                );
                
            $this->info('✅ CSR and private key generated successfully!');
            $this->line('CSR file: ' . storage_path('zatca/dev/certificate.csr'));
            $this->line('Private key: ' . storage_path('zatca/dev/private.pem'));
            $this->info('');
            $this->info('Next steps:');
            $this->line('1. Submit the CSR to ZATCA portal');
            $this->line('2. Get the OTP from ZATCA');
            $this->line('3. Run: php artisan zatca:request-compliance <OTP>');
            
        } catch (CertificateBuilderException $e) {
            $this->error('❌ Error generating CSR: ' . $e->getMessage());
            return 1;
        }
        
        return 0;
    }
}