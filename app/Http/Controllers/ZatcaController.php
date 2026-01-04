<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Saleh7\Zatca\CertificateBuilder;
use Saleh7\Zatca\Exceptions\CertificateBuilderException;
use App\Services\Zatca\CertificateRequester;
use App\Services\Zatca\InvoiceXmlGenerator;
use App\Services\Zatca\InvoiceSignerService;
use App\Services\Zatca\InvoiceSubmitter;
use App\Models\Invoicedata;

class ZatcaController extends Controller
{
    // 1. Generate CSR + private key
    public function generateCsr()
    {
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

            return response()->json([
                'success' => true,
                'message' => 'CSR and private key generated successfully!',
                'files' => [
                    'csr' => storage_path('zatca/dev/certificate.csr'),
                    'private_key' => storage_path('zatca/dev/private.pem')
                ]
            ]);

        } catch (CertificateBuilderException $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }

    // 2. Request compliance certificate
    public function requestCompliance($otp)
    {
        try {
            $requester = new CertificateRequester();
            $result = $requester->request($otp);

            return response()->json([
                'success' => true,
                'message' => $result,
                'file' => storage_path('zatca/dev/ZATCA_certificate_data.json')
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }

    // 3. Build + sign + submit invoice
    public function submitInvoice($invoiceId)
    {
        try {
            // fetch invoice data
            $invoice = Invoicedata::findOrFail($invoiceId);

            // build XML
            $xmlGenerator = new InvoiceXmlGenerator();
            $unsignedXml = $xmlGenerator->generate($invoice);

            // sign XML
            $signer = new InvoiceSignerService();
            $signedXml = $signer->sign($unsignedXml, $invoice);

            // submit invoice to ZATCA
            $submitter = new InvoiceSubmitter();
            $response = $submitter->submit($signedXml, $invoice);

            return response()->json([
                'success' => true,
                'zatca_response' => $response
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
