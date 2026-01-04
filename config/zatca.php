<?php

return [
    // Current development settings
    'certificate_path' => storage_path('zatca/dev/certificate.pem'),
    'private_key_path' => storage_path('zatca/dev/private_ec.pem'),
    'secret' => env('ZATCA_SECRET', 'mock-secret-key-12345'),
    'environment' => env('ZATCA_ENVIRONMENT', 'sandbox'),
    
    // SDK configuration with your exact path
    'sdk' => [
        'path' => 'C:\Users\mhmds\Netplex Solutions\Taxfly\Zatca\zatca-envoice-sdk-203\zatca-envoice-sdk-203',
        'java_path' => 'java', // Java in PATH
        'temp_path' => storage_path('zatca/temp'),
        'certificates_path' => 'C:\Users\mhmds\Netplex Solutions\Taxfly\Zatca\zatca-envoice-sdk-203\zatca-envoice-sdk-203\Data\Certificates',
    ],
    
    // CSR configuration
    'csr_config' => [
        'common_name' => env('ZATCA_CSR_COMMON_NAME', 'Your Company Name'),
        'serial_number' => env('ZATCA_CSR_SERIAL_NUMBER', '1234567891'),
        'organization_identifier' => env('ZATCA_CSR_ORG_ID', '1234567891'),
        'organization_unit_name' => env('ZATCA_CSR_ORG_UNIT', 'Sales'),
        'organization_name' => env('ZATCA_CSR_ORG_NAME', 'Your Company Ltd'),
        'country_name' => env('ZATCA_CSR_COUNTRY', 'SA'),
        'invoice_type' => env('ZATCA_CSR_INVOICE_TYPE', '0100'),
        'location_address' => env('ZATCA_CSR_LOCATION', 'Riyadh, Saudi Arabia'),
        'industry_business_category' => env('ZATCA_CSR_INDUSTRY', 'Retail')
    ],
];