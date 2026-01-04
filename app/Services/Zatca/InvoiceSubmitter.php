<?php

namespace App\Services\Zatca;

use Saleh7\Zatca\ZatcaAPI;
use Saleh7\Zatca\Storage;

class InvoiceSubmitter
{
    /**
     * Submit a signed invoice to ZATCA.
     *
     * @param string      $certificate The certificate string for authentication
     * @param string      $secret      The API secret
     * @param string      $invoiceHash The invoice hash (base64)
     * @param string      $uuid        Unique invoice identifier
     * @param string|null $signedInvoiceFile Optional path to signed invoice XML
     * @return array API response
     * @throws \Exception
     */
    public function submit(
        string $certificate,
        string $secret,
        string $invoiceHash,
        string $uuid,
        ?string $signedInvoiceFile = null
    ): array {
        $storage = new Storage();
        $signedInvoicePath = $signedInvoiceFile ?? storage_path('zatca/signed_invoice.xml');

        $signedInvoice = $storage->get($signedInvoicePath);

        if ($signedInvoice === false) {
            throw new \Exception("Signed invoice XML not found at {$signedInvoicePath}");
        }

        $zatcaClient = new ZatcaAPI(env('ZATCA_ENV', 'sandbox')); // use sandbox/production dynamically

        return $zatcaClient->submitClearanceInvoice(
            $certificate,
            $secret,
            $signedInvoice, // now guaranteed to be string
            $invoiceHash,
            $uuid
        );
    }
}
