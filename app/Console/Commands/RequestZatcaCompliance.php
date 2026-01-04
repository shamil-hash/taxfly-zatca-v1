<?php
// app/Console/Commands/RequestZatcaCompliance.php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\Zatca\CertificateRequester;

class RequestZatcaCompliance extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'zatca:request-compliance {otp? : The OTP received from ZATCA (optional for development)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Request ZATCA compliance certificate using OTP';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $otp = $this->argument('otp');
        
        // Check if CSR file exists
        $csrPath = storage_path('zatca/dev/certificate.csr');
        if (!file_exists($csrPath)) {
            $this->error('❌ CSR file not found. Please run php artisan zatca:generate-csr first.');
            return 1;
        }

        // Handle development mode (no OTP provided)
        if (empty($otp)) {
            if (app()->environment('production')) {
                $this->error('❌ OTP is required for production environment.');
                return 1;
            }
            
            $this->info('Development mode: Using test OTP 123456...');
            $otp = '123456'; // Use ZATCA's standard test OTP
        }
        
        $this->info('Requesting ZATCA compliance certificate with OTP: ' . $otp);

        try {
            $requester = new CertificateRequester();
            $result = $requester->request($otp);
            
            $this->info('✅ ' . $result);
            $this->line('Certificate data saved to: ' . storage_path('zatca/dev/ZATCA_certificate_data.json'));
            $this->info('');
            $this->info('You can now generate ZATCA-compliant invoices!');
            
        } catch (\Exception $e) {
            $this->error('❌ Error requesting compliance certificate: ' . $e->getMessage());
            return 1;
        }
        
        return 0;
    }
}