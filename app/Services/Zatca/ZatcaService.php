<?php

namespace App\Services\Zatca;

use App\Services\Zatca\InvoiceXmlGenerator;
use App\Services\Zatca\InvoiceSignerService;
use App\Services\Zatca\InvoiceQrGenerator;
use App\Services\Zatca\InvoiceSubmitter;
use Saleh7\Zatca\Helpers\Certificate;
use Saleh7\Zatca\Storage;

use Illuminate\Support\Str;

class ZatcaService
{
    protected InvoiceXmlGenerator $xmlGenerator;
    protected InvoiceSignerService $signer;
    protected InvoiceQrGenerator $qrGenerator;
    protected InvoiceSubmitter $submitter;
    protected Storage $storage;

    public function __construct(
        InvoiceXmlGenerator $xmlGenerator,
        InvoiceSignerService $signer,
        InvoiceQrGenerator $qrGenerator,
        InvoiceSubmitter $submitter
    ) {
        $this->xmlGenerator = $xmlGenerator;
        $this->signer       = $signer;
        $this->qrGenerator  = $qrGenerator;
        $this->submitter    = $submitter;
        $this->storage      = new Storage();
    }

    /**
     * Step 3 → 5 pipeline: Generate → Sign → QR
     *
     * @param array $invoiceData
     * @return array
     */
    public function generateComplianceInvoice(array $invoiceData): array
    {
        // --- Ensure storage folders exist ---
       $zatcaDevPath = storage_path('zatca/dev');
        $jsonPath = $zatcaDevPath . '/ZATCA_certificate_data.json';
        
        if (!file_exists($jsonPath)) {
            throw new \RuntimeException(
                "ZATCA compliance certificate not found. " .
                "Please complete the compliance process first. " .
                "Expected file: " . $jsonPath
            );
        }

        // --- Load dev certificate JSON ---
        $jsonPath = $zatcaDevPath . '/ZATCA_certificate_data.json';
        if (!file_exists($jsonPath)) {
            throw new \RuntimeException("ZATCA certificate JSON not found at $jsonPath. Please complete the compliance process first.");
        }
        
        $jsonCertificate = file_get_contents($jsonPath);
        $jsonData = json_decode($jsonCertificate, true, 512, JSON_THROW_ON_ERROR);
        
        // Validate JSON structure
        if (!isset($jsonData['certificate']) || empty($jsonData['certificate'])) {
            throw new \RuntimeException("Invalid ZATCA certificate data: 'certificate' field missing or empty");
        }
        
        if (!isset($jsonData['secret']) || empty($jsonData['secret'])) {
            throw new \RuntimeException("Invalid ZATCA certificate data: 'secret' field missing or empty");
        }

        // --- Load private key ---
        $privateKeyPath = $zatcaDevPath . '/private.pem';
        if (!file_exists($privateKeyPath)) {
            throw new \RuntimeException("ZATCA private key not found at $privateKeyPath");
        }
        
        // DO NOT strip the PEM headers - keep the private key intact
        $privateKey = file_get_contents($privateKeyPath);
        
        // Validate private key format
        if (!str_contains($privateKey, '-----BEGIN PRIVATE KEY-----')) {
            throw new \RuntimeException("Private key is not in proper PEM format");
        }

        // --- Build Certificate object ---
        $certificate = new Certificate(
            $jsonData['certificate'],
            $privateKey,
            $jsonData['secret']
        );

        // --- Generate, sign, and create QR ---
        $result = $this->xmlGenerator->generate(
            $invoiceData,
            $certificate,
            $invoiceData['branch_number'] ?? null
        );

        // --- Generate UUID if not already present ---
        $uuid = $invoiceData['uuid'] ?? \Illuminate\Support\Str::uuid()->toString();

        return [
            'unsigned_xml' => $result['unsigned_xml'],
            'signed_xml'   => $result['signed_xml'],
            'qr_base64'    => $result['qr_base64'],
            'invoice_hash' => $result['invoice_hash'],
            'uuid'         => $uuid,
        ];
    }

    /**
     * Step 6: Submit to ZATCA API (Clearance/Reporting)
     *
     * @param array $invoiceData
     * @return array
     */
    public function submitToZatca(array $invoiceData): array
    {
        $jsonCertificate = $this->storage->get(storage_path('zatca/dev/ZATCA_certificate_data.json'));
        $jsonData = json_decode($jsonCertificate, true, 512, JSON_THROW_ON_ERROR);

        return $this->submitter->submit(
            $jsonData['certificate'],
            $jsonData['secret'],
            $invoiceData['invoice_hash'],
            $invoiceData['uuid'],
            $invoiceData['signed_invoice_file'] ?? null
        );
    }
}
