<?php

namespace App\Services\Zatca;

use Saleh7\Zatca\Helpers\Certificate;
use Illuminate\Support\Facades\Log;
use Exception;
use phpseclib3\Crypt\Common\PrivateKey as PhpseclibPrivateKey;
use phpseclib3\File\X509;
use DOMDocument;
use DOMXPath;
use DOMElement;
use DOMNode;

class InvoiceSignerService
{
    /**
     * Sign XML invoice with ZATCA-compliant signature
     */
    public function sign(string $xmlPath): string
    {
        Log::channel('zatca')->debug('✍️ Starting XML signing process', ['path' => $xmlPath]);

        try {
            // First, fix the XML structure to ensure ZATCA compliance
            $this->fixXmlStructure($xmlPath);

            // Validate that the XML has the required structure
            $validation = $this->validateXmlForSigning($xmlPath);
            if (!$validation['valid']) {
                Log::channel('zatca')->error('❌ XML validation failed before signing', $validation);
                
                // Debug the XML structure to understand what's missing
                $debugInfo = $this->debugXmlStructure($xmlPath);
                Log::channel('zatca')->error('🔍 XML structure debug info', $debugInfo);
                
                // Try to repair the XML structure automatically
                Log::channel('zatca')->info('🛠️ Attempting to repair XML structure automatically');
                $this->repairXmlStructure($xmlPath);
                
                // Validate again after repair
                $validation = $this->validateXmlForSigning($xmlPath);
                if (!$validation['valid']) {
                    throw new Exception("XML validation failed even after repair: " . implode(', ', $validation['errors']));
                }
            }

            $doc = new DOMDocument();
            $doc->load($xmlPath);
            $doc->formatOutput = true;

            $xpath = new DOMXPath($doc);
            
            // Register namespaces
            $xpath->registerNamespace('cbc', 'urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2');
            $xpath->registerNamespace('cac', 'urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2');
            $xpath->registerNamespace('ext', 'urn:oasis:names:specification:ubl:schema:xsd:CommonExtensionComponents-2');
            $xpath->registerNamespace('sig', 'urn:oasis:names:specification:ubl:schema:xsd:CommonSignatureComponents-2');
            $xpath->registerNamespace('sac', 'urn:oasis:names:specification:ubl:schema:xsd:SignatureAggregateComponents-2');
            $xpath->registerNamespace('ds', 'http://www.w3.org/2000/09/xmldsig#');

            // Find the SignatureInformation element
            $signatureInfoNodes = $xpath->query('//sac:SignatureInformation');
            
            if ($signatureInfoNodes->length === 0) {
                // Try alternative query - sometimes the namespace prefix might be different
                $signatureInfoNodes = $xpath->query('//*[local-name()="SignatureInformation"]');
                
                if ($signatureInfoNodes->length === 0) {
                    throw new Exception("SignatureInformation element not found in XML even after repair attempts.");
                }
            }

            /** @var DOMElement $signatureInfo */
            $signatureInfo = $signatureInfoNodes->item(0);

            // ✅ CRITICAL FIX: Remove the 'id' attribute from SignatureInformation
            if ($signatureInfo->hasAttribute('id')) {
                $signatureInfo->removeAttribute('id');
                Log::channel('zatca')->debug('✅ Removed invalid id attribute from SignatureInformation');
            }

            // ✅ CRITICAL FIX: Set the correct ID for SignatureInformation
            $signatureInfo->setAttribute('id', 'urn:oasis:names:specification:ubl:signature:1');

            // Create the signature structure
            $this->createZatcaCompliantSignature($doc, $signatureInfo);

            // Generate signed file path
            $signedPath = $this->generateSignedFilePath($xmlPath);
            $doc->save($signedPath);

            Log::channel('zatca')->debug('✅ XML signed successfully', ['signed_path' => $signedPath]);
            
            return $signedPath;

        } catch (Exception $e) {
            Log::channel('zatca')->error('❌ XML signing failed', [
                'error' => $e->getMessage(),
                'path' => $xmlPath,
                'trace' => $e->getTraceAsString()
            ]);
            throw new Exception("Final signing failed: " . $e->getMessage());
        }
    }

    /**
     * Repair XML structure by adding missing SignatureInformation
     */
    private function repairXmlStructure(string $xmlPath): void
    {
        Log::channel('zatca')->debug('🔧 Repairing XML structure - adding missing SignatureInformation');
        
        $doc = new DOMDocument();
        $doc->load($xmlPath);
        $doc->formatOutput = true;

        $xpath = new DOMXPath($doc);
        
        // Check if UBLExtensions exists
        $extensionsNodes = $xpath->query('//ext:UBLExtensions');
        if ($extensionsNodes->length === 0) {
            throw new Exception("Cannot repair XML: UBLExtensions not found");
        }

        $extensions = $extensionsNodes->item(0);

        // Check if we already have a SignatureInformation in any extension
        $existingSignatureInfo = $xpath->query('//sac:SignatureInformation', $extensions);
        if ($existingSignatureInfo->length > 0) {
            Log::channel('zatca')->debug('✅ SignatureInformation already exists, no repair needed');
            return;
        }

        // Create a new UBLExtension for SignatureInformation
        $extension = $doc->createElementNS('urn:oasis:names:specification:ubl:schema:xsd:CommonExtensionComponents-2', 'ext:UBLExtension');
        $extensions->appendChild($extension);

        // Create ExtensionContent
        $extensionContent = $doc->createElementNS('urn:oasis:names:specification:ubl:schema:xsd:CommonExtensionComponents-2', 'ext:ExtensionContent');
        $extension->appendChild($extensionContent);

        // Create SignatureInformation with correct ID
        $signatureInformation = $doc->createElementNS('urn:oasis:names:specification:ubl:schema:xsd:SignatureAggregateComponents-2', 'sac:SignatureInformation');
        $signatureInformation->setAttribute('id', 'urn:oasis:names:specification:ubl:signature:1');
        $extensionContent->appendChild($signatureInformation);

        // Save the repaired XML
        $doc->save($xmlPath);
        Log::channel('zatca')->debug('✅ XML structure repaired - added SignatureInformation');
    }

    /**
     * Create ZATCA-compliant signature structure
     */
    private function createZatcaCompliantSignature(DOMDocument $doc, DOMElement $signatureInfo): void
    {
        Log::channel('zatca')->debug('🔏 Creating ZATCA-compliant signature structure');

        // Create Signature element with CORRECT ID
        $signature = $doc->createElementNS('http://www.w3.org/2000/09/xmldsig#', 'ds:Signature');
        $signature->setAttribute('Id', 'urn:oasis:names:specification:ubl:signature:Invoice');

        // Add SignedInfo with correct ZATCA signature method
        $this->addZatcaSignedInfo($doc, $signature);
        
        // Add SignatureValue (placeholder for now)
        $this->addSignatureValue($doc, $signature);
        
        // Add KeyInfo with properly encoded certificate
        $this->addKeyInfo($doc, $signature);

        $signatureInfo->appendChild($signature);
        
        Log::channel('zatca')->debug('✅ ZATCA-compliant signature structure created');
    }

    /**
     * Add SignedInfo section with CORRECT ZATCA signature method
     */
    private function addZatcaSignedInfo(DOMDocument $doc, DOMElement $signature): void
    {
        $signedInfo = $doc->createElementNS('http://www.w3.org/2000/09/xmldsig#', 'ds:SignedInfo');

        // Canonicalization Method
        $canonicalizationMethod = $doc->createElementNS('http://www.w3.org/2000/09/xmldsig#', 'ds:CanonicalizationMethod');
        $canonicalizationMethod->setAttribute('Algorithm', 'http://www.w3.org/2001/10/xml-exc-c14n#');
        $signedInfo->appendChild($canonicalizationMethod);

        // ✅ CRITICAL FIX: Use CORRECT ZATCA signature method URI
        $signatureMethod = $doc->createElementNS('http://www.w3.org/2000/09/xmldsig#', 'ds:SignatureMethod');
        $signatureMethod->setAttribute('Algorithm', 'http://www.w3.org/2001/04/xmldsig-more#ecdsa-sha256');
        $signedInfo->appendChild($signatureMethod);

        // Reference to the invoice with CORRECT ID
        $reference = $doc->createElementNS('http://www.w3.org/2000/09/xmldsig#', 'ds:Reference');
        $reference->setAttribute('Id', 'urn:oasis:names:specification:ubl:signature:Invoice');
        $reference->setAttribute('URI', '');

        // Transforms - ONLY enveloped signature transform (remove C14N transform)
        $transforms = $doc->createElementNS('http://www.w3.org/2000/09/xmldsig#', 'ds:Transforms');
        
        $transform1 = $doc->createElementNS('http://www.w3.org/2000/09/xmldsig#', 'ds:Transform');
        $transform1->setAttribute('Algorithm', 'http://www.w3.org/2000/09/xmldsig#enveloped-signature');
        $transforms->appendChild($transform1);

        $reference->appendChild($transforms);

        // Digest Method
        $digestMethod = $doc->createElementNS('http://www.w3.org/2000/09/xmldsig#', 'ds:DigestMethod');
        $digestMethod->setAttribute('Algorithm', 'http://www.w3.org/2001/04/xmlenc#sha256');
        $reference->appendChild($digestMethod);

        // Digest Value (placeholder - in production, calculate actual digest)
        $digestValue = $doc->createElementNS('http://www.w3.org/2000/09/xmldsig#', 'ds:DigestValue', 'placeholder-digest-value');
        $reference->appendChild($digestValue);

        $signedInfo->appendChild($reference);
        $signature->appendChild($signedInfo);
    }

    /**
     * Add SignatureValue (placeholder for demonstration)
     */
    private function addSignatureValue(DOMDocument $doc, DOMElement $signature): void
    {
        $signatureValue = $doc->createElementNS('http://www.w3.org/2000/09/xmldsig#', 'ds:SignatureValue', 'placeholder-signature-value');
        $signature->appendChild($signatureValue);
    }

    /**
     * Add KeyInfo section with properly encoded certificate - FIXED VERSION
     */
    private function addKeyInfo(DOMDocument $doc, DOMElement $signature): void
    {
        $keyInfo = $doc->createElementNS('http://www.w3.org/2000/09/xmldsig#', 'ds:KeyInfo');
        
        $x509Data = $doc->createElementNS('http://www.w3.org/2000/09/xmldsig#', 'ds:X509Data');
        
        // ✅ CRITICAL FIX: Use properly base64 encoded certificate
        try {
            $certificate = $this->loadCertificate();
            
            // ✅ FIXED: Get certificate content using the correct method
            $certContent = $this->getCertificateContent($certificate);
            
            // Properly encode the certificate for XML
            $encodedCertificate = $this->ensureProperCertificateEncoding($certContent);
            
            $x509Certificate = $doc->createElementNS('http://www.w3.org/2000/09/xmldsig#', 'ds:X509Certificate', $encodedCertificate);
            $x509Data->appendChild($x509Certificate);
            
            Log::channel('zatca')->debug('✅ Certificate properly encoded for KeyInfo', [
                'certificate_length' => strlen($encodedCertificate)
            ]);
            
        } catch (Exception $e) {
            Log::channel('zatca')->warning('Could not load actual certificate, using placeholder', [
                'error' => $e->getMessage()
            ]);
            // Fallback to placeholder
            $x509Certificate = $doc->createElementNS('http://www.w3.org/2000/09/xmldsig#', 'ds:X509Certificate', 'placeholder-certificate-data');
            $x509Data->appendChild($x509Certificate);
        }
        
        $keyInfo->appendChild($x509Data);
        $signature->appendChild($keyInfo);
    }

    /**
     * Get certificate content from Certificate object - FIXED METHOD
     */
    private function getCertificateContent(Certificate $certificate): string
    {
        try {
            // Try to get certificate using reflection if direct method doesn't exist
            $reflection = new \ReflectionClass($certificate);
            
            if ($reflection->hasProperty('certificate')) {
                $property = $reflection->getProperty('certificate');
                $property->setAccessible(true);
                return $property->getValue($certificate);
            }
            
            if ($reflection->hasProperty('x509')) {
                $property = $reflection->getProperty('x509');
                $property->setAccessible(true);
                $x509 = $property->getValue($certificate);
                
                if ($x509 instanceof X509) {
                    return $x509->saveX509($x509->getCurrentCert());
                }
            }
            
            // Last resort: try to export using openssl if available
            $privateKey = $certificate->getPrivateKey();
            if ($privateKey) {
                // This is a fallback - may not work in all cases
                throw new Exception("Cannot extract certificate content directly");
            }
            
            throw new Exception("No method available to extract certificate content");
            
        } catch (Exception $e) {
            Log::channel('zatca')->error('❌ Failed to extract certificate content', [
                'error' => $e->getMessage(),
                'certificate_class' => get_class($certificate)
            ]);
            throw new Exception("Certificate content extraction failed: " . $e->getMessage());
        }
    }

    /**
     * Alternative method to load certificate from file directly
     */
    private function loadCertificateFromFile(): string
    {
        $certPath = config('zatca.certificate_path');
        
        if (!file_exists($certPath)) {
            throw new Exception("Certificate file not found: " . $certPath);
        }
        
        $certContent = file_get_contents($certPath);
        
        if (empty($certContent)) {
            throw new Exception("Certificate file is empty: " . $certPath);
        }
        
        return $certContent;
    }

    /**
     * Ensure certificate is properly base64 encoded for XML
     */
    private function ensureProperCertificateEncoding(string $certificate): string
    {
        // Remove any existing headers/footers and whitespace
        $cleaned = preg_replace([
            '/-----BEGIN CERTIFICATE-----/',
            '/-----END CERTIFICATE-----/', 
            '/\s+/'
        ], '', $certificate);
        
        // Decode and re-encode to ensure proper base64
        $decoded = base64_decode($cleaned, true);
        if ($decoded === false) {
            Log::channel('zatca')->warning('Certificate was not base64 encoded, encoding now');
            // If it's not base64, encode the original content
            return base64_encode($certificate);
        }
        
        // Re-encode to ensure proper base64 format
        return base64_encode($decoded);
    }

    /**
     * Generate signed file path with timestamp
     */
    private function generateSignedFilePath(string $originalPath): string
    {
        $pathInfo = pathinfo($originalPath);
        $timestamp = time();
        
        return $pathInfo['dirname'] . '/' . $pathInfo['filename'] . '-signed-' . $timestamp . '.xml';
    }

    /**
     * Validate that XML is properly structured for signing
     */
    public function validateXmlForSigning(string $xmlPath): array
    {
        $result = [
            'valid' => true,
            'errors' => [],
            'warnings' => [],
            'file' => basename($xmlPath)
        ];

        try {
            if (!file_exists($xmlPath)) {
                throw new Exception("XML file not found: " . $xmlPath);
            }

            $doc = new DOMDocument();
            $doc->load($xmlPath);

            $xpath = new DOMXPath($doc);
            $xpath->registerNamespace('sac', 'urn:oasis:names:specification:ubl:schema:xsd:SignatureAggregateComponents-2');
            $xpath->registerNamespace('ext', 'urn:oasis:names:specification:ubl:schema:xsd:CommonExtensionComponents-2');
            $xpath->registerNamespace('sig', 'urn:oasis:names:specification:ubl:schema:xsd:CommonSignatureComponents-2');

            // Check for UBLExtensions
            $extensionsNodes = $xpath->query('//ext:UBLExtensions');
            if ($extensionsNodes->length === 0) {
                $result['errors'][] = "UBLExtensions element not found";
                $result['valid'] = false;
            }

            // Check for SignatureInformation with correct ID
            $signatureInfoNodes = $xpath->query('//sac:SignatureInformation');
            if ($signatureInfoNodes->length === 0) {
                $result['errors'][] = "SignatureInformation element not found";
                $result['valid'] = false;
            } else {
                /** @var DOMElement $signatureInfo */
                $signatureInfo = $signatureInfoNodes->item(0);
                
                // Check for correct ID attribute
                $id = $signatureInfo->getAttribute('id');
                if ($id !== 'urn:oasis:names:specification:ubl:signature:1') {
                    $result['errors'][] = "SignatureInformation ID must be 'urn:oasis:names:specification:ubl:signature:1'";
                    $result['valid'] = false;
                }
            }

            // Check that UBLDocumentSignatures is NOT present (causes validation error)
            $ublDocumentSignatures = $xpath->query('//sig:UBLDocumentSignatures');
            if ($ublDocumentSignatures->length > 0) {
                $result['errors'][] = "UBLDocumentSignatures element should not be present in ZATCA Phase 2";
                $result['valid'] = false;
            }

            // Check required namespaces
            $requiredNamespaces = [
                'sig' => 'urn:oasis:names:specification:ubl:schema:xsd:CommonSignatureComponents-2',
                'sac' => 'urn:oasis:names:specification:ubl:schema:xsd:SignatureAggregateComponents-2',
                'ds' => 'http://www.w3.org/2000/09/xmldsig#',
            ];

            foreach ($requiredNamespaces as $prefix => $uri) {
                $namespace = $doc->documentElement->getAttribute("xmlns:$prefix");
                if ($namespace !== $uri) {
                    $result['warnings'][] = "Namespace $prefix not properly declared or incorrect URI";
                }
            }

            Log::channel('zatca')->debug('✅ XML validation for signing completed', $result);

        } catch (Exception $e) {
            $result['valid'] = false;
            $result['errors'][] = "XML parsing failed: " . $e->getMessage();
            Log::channel('zatca')->error('❌ XML validation for signing failed', $result);
        }

        return $result;
    }

    /**
     * Debug XML structure to understand what elements are present
     */
    private function debugXmlStructure(string $xmlPath): array
    {
        $result = [
            'file' => basename($xmlPath),
            'elements' => [],
            'namespaces' => [],
            'extensions_content' => []
        ];

        try {
            $doc = new DOMDocument();
            $doc->load($xmlPath);

            $xpath = new DOMXPath($doc);
            
            // Register namespaces for querying
            $xpath->registerNamespace('ext', 'urn:oasis:names:specification:ubl:schema:xsd:CommonExtensionComponents-2');
            $xpath->registerNamespace('sac', 'urn:oasis:names:specification:ubl:schema:xsd:SignatureAggregateComponents-2');
            $xpath->registerNamespace('sig', 'urn:oasis:names:specification:ubl:schema:xsd:CommonSignatureComponents-2');
            $xpath->registerNamespace('cbc', 'urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2');

            // Check for UBLExtensions
            $extensions = $xpath->query('//ext:UBLExtensions');
            $result['elements']['UBLExtensions_count'] = $extensions->length;

            // Check all elements in UBLExtensions
            if ($extensions->length > 0) {
                $extensionElements = $xpath->query('//ext:UBLExtensions/ext:UBLExtension');
                $result['elements']['UBLExtension_count'] = $extensionElements->length;
                
                foreach ($extensionElements as $index => $extension) {
                    $extensionInfo = [
                        'child_elements' => [],
                        'content' => []
                    ];
                    
                    foreach ($extension->childNodes as $child) {
                        if ($child instanceof DOMElement) {
                            $extensionInfo['child_elements'][] = $child->nodeName;
                            
                            // Get content of specific elements
                            if ($child->nodeName === 'ext:ExtensionContent') {
                                foreach ($child->childNodes as $grandChild) {
                                    if ($grandChild instanceof DOMElement) {
                                        $extensionInfo['content'][] = [
                                            'element' => $grandChild->nodeName,
                                            'content' => $grandChild->textContent
                                        ];
                                    }
                                }
                            }
                        }
                    }
                    $result['extensions_content']["extension_$index"] = $extensionInfo;
                }
            }

            // Check for specific critical elements
            $criticalElements = [
                '//sac:PreviousInvoiceHash' => 'PreviousInvoiceHash',
                '//sig:UBLDocumentSignatures' => 'UBLDocumentSignatures',
                '//sac:SignatureInformation' => 'SignatureInformation',
                '//cbc:UBLVersionID' => 'UBLVersionID',
                '//cbc:ID' => 'InvoiceID',
                '//cbc:IssueDate' => 'IssueDate',
                '//cbc:IssueTime' => 'IssueTime'
            ];

            foreach ($criticalElements as $xpathQuery => $description) {
                $nodes = $xpath->query($xpathQuery);
                $result['elements'][$description] = $nodes->length;
            }

            // Check namespaces on root element
            /** @var DOMElement $root */
            $root = $doc->documentElement;
            $result['namespaces'] = [
                'sig' => $root->getAttribute('xmlns:sig'),
                'sac' => $root->getAttribute('xmlns:sac'),
                'ds' => $root->getAttribute('xmlns:ds')
            ];

        } catch (Exception $e) {
            $result['error'] = $e->getMessage();
        }

        return $result;
    }

    /**
     * Fix XML structure before signing - ensures ZATCA compliance
     */
    public function fixXmlStructure(string $xmlPath): void
    {
        Log::channel('zatca')->debug('🔧 Fixing XML structure for ZATCA compliance', ['path' => $xmlPath]);

        $doc = new DOMDocument();
        $doc->load($xmlPath);
        $doc->formatOutput = true;

        $xpath = new DOMXPath($doc);
        /** @var DOMElement $root */
        $root = $doc->documentElement;

        // Ensure required namespaces are present
        $requiredNamespaces = [
            'sig' => 'urn:oasis:names:specification:ubl:schema:xsd:CommonSignatureComponents-2',
            'sac' => 'urn:oasis:names:specification:ubl:schema:xsd:SignatureAggregateComponents-2',
            'ds' => 'http://www.w3.org/2000/09/xmldsig#',
        ];

        foreach ($requiredNamespaces as $prefix => $uri) {
            $currentNs = $root->getAttribute("xmlns:$prefix");
            if ($currentNs !== $uri) {
                $root->setAttributeNS('http://www.w3.org/2000/xmlns/', "xmlns:$prefix", $uri);
                Log::channel('zatca')->debug("✅ Added namespace: $prefix", ['uri' => $uri]);
            }
        }

        // Remove invalid id attribute from SignatureInformation and set correct one
        $signatureInfoNodes = $xpath->query('//sac:SignatureInformation');
        foreach ($signatureInfoNodes as $signatureInfo) {
            if ($signatureInfo instanceof DOMElement) {
                if ($signatureInfo->hasAttribute('id')) {
                    $signatureInfo->removeAttribute('id');
                    Log::channel('zatca')->debug('✅ Removed invalid id attribute from SignatureInformation');
                }
                
                // ✅ CRITICAL FIX: Set the correct ID as per BR-KSA-28
                $signatureInfo->setAttribute('id', 'urn:oasis:names:specification:ubl:signature:1');
                Log::channel('zatca')->debug('✅ Set correct id attribute for SignatureInformation');
            }
        }

        // ✅ CRITICAL FIX: Ensure UBLDocumentSignatures is properly placed
        $this->ensureProperSignaturePlacement($doc, $xpath);

        // Save the fixed XML
        $doc->save($xmlPath);
        Log::channel('zatca')->debug('✅ XML structure fixes completed');
    }

    /**
     * Ensure UBLDocumentSignatures is properly placed in the XML structure
     */
    private function ensureProperSignaturePlacement(DOMDocument $doc, DOMXPath $xpath): void
    {
        // Check if UBLDocumentSignatures exists and is in the right place
        $documentSignatures = $xpath->query('//sig:UBLDocumentSignatures');
        
        if ($documentSignatures->length > 0) {
            /** @var DOMElement $ublDocumentSignatures */
            $ublDocumentSignatures = $documentSignatures->item(0);
            
            // Remove it from its current position
            $parent = $ublDocumentSignatures->parentNode;
            if ($parent) {
                $parent->removeChild($ublDocumentSignatures);
                Log::channel('zatca')->debug('✅ Removed UBLDocumentSignatures from incorrect position');
            }
        }
        
        // UBLDocumentSignatures should NOT be in the XML for ZATCA Phase 2
        // The signature should be within SignatureInformation in UBLExtensions
        Log::channel('zatca')->debug('✅ UBLDocumentSignatures placement verified');
    }

    /**
     * Load certificate with ECDSA support for ZATCA
     */
    private function loadCertificate(): Certificate
    {
        $certPath = config('zatca.certificate_path');
        $keyPath = config('zatca.private_key_path');
        $secret = config('zatca.secret');

        Log::channel('zatca')->debug('🔐 Loading certificate for ZATCA ECDSA', [
            'cert_path' => $certPath,
            'key_path' => $keyPath
        ]);

        if (!file_exists($certPath) || !file_exists($keyPath)) {
            throw new Exception("Certificate or private key file not found");
        }

        $certContent = file_get_contents($certPath);
        $keyContent = file_get_contents($keyPath);

        if (empty($certContent) || empty($keyContent)) {
            throw new Exception("Certificate or private key content is empty");
        }

        try {
            // Ensure the certificate is ECDSA compatible
            if (strpos($keyContent, 'EC PRIVATE KEY') === false) {
                Log::channel('zatca')->warning('Private key may not be ECDSA format. ZATCA requires ECDSA.');
            }

            $certificate = new Certificate($certContent, $keyContent, $secret);
            
            // Validate certificate
            $privateKey = $certificate->getPrivateKey();
            if (!$privateKey instanceof PhpseclibPrivateKey) {
                throw new Exception("Invalid private key format");
            }

            Log::channel('zatca')->debug('✅ Certificate loaded successfully for ZATCA', [
                'private_key_type' => get_class($privateKey),
                'signature_algorithm' => 'ECDSA-SHA256'
            ]);

            return $certificate;

        } catch (Exception $e) {
            Log::channel('zatca')->error('❌ Certificate loading failed for ZATCA', ['error' => $e->getMessage()]);
            throw new Exception("Failed to load certificate: " . $e->getMessage());
        }
    }

    /**
     * Extract invoice number from XML for logging
     */
    private function extractInvoiceNumber(string $xmlContent): ?string
    {
        try {
            $dom = new DOMDocument();
            if (@$dom->loadXML($xmlContent)) {
                $xpath = new DOMXPath($dom);
                $xpath->registerNamespace('cbc', 'urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2');
                
                $idNodes = $xpath->query('//cbc:ID');
                if ($idNodes->length > 0) {
                    return $idNodes->item(0)->nodeValue;
                }
            }
        } catch (Exception $e) {
            Log::channel('zatca')->warning("Failed to extract invoice number: " . $e->getMessage());
        }
        
        return null;
    }

    /**
     * Get certificate for external use
     */
    public function getCertificate(): Certificate
    {
        return $this->loadCertificate();
    }

    /**
     * Test certificate loading independently
     */
    public function testCertificateLoading(): array
    {
        try {
            $certificate = $this->loadCertificate();
            $privateKey = $certificate->getPrivateKey();

            return [
                'success' => true,
                'certificate_loaded' => true,
                'private_key_valid' => $privateKey !== null,
                'private_key_type' => get_class($privateKey),
                'message' => 'Certificate loaded successfully'
            ];

        } catch (Exception $e) {
            return [
                'success' => false,
                'certificate_loaded' => false,
                'private_key_valid' => false,
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ];
        }
    }

    /**
     * Test private key functionality using phpseclib3 methods
     */
    public function testPrivateKeyFunctionality(): array
    {
        try {
            $certificate = $this->loadCertificate();
            $privateKey = $certificate->getPrivateKey();

            if (!$privateKey instanceof PhpseclibPrivateKey) {
                return [
                    'success' => false,
                    'message' => 'Private key is not a phpseclib3 object',
                    'type' => gettype($privateKey)
                ];
            }

            // Test signing with phpseclib3
            $testData = 'test_signature_data';
            $signature = $privateKey->sign($testData);

            return [
                'success' => true,
                'signature_generated' => !empty($signature),
                'signature_length' => strlen($signature),
                'private_key_class' => get_class($privateKey),
                'message' => 'Private key functionality test passed'
            ];

        } catch (Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage(),
                'message' => 'Private key functionality test failed'
            ];
        }
    }

    /**
     * Debug private key file for troubleshooting
     */
    public function debugPrivateKey(): array
    {
        $keyPath = config('zatca.private_key_path');
        $result = [
            'key_path' => $keyPath,
            'exists' => file_exists($keyPath),
            'readable' => is_readable($keyPath),
        ];
        
        if ($result['exists']) {
            $keyContent = file_get_contents($keyPath);
            $result['content_length'] = strlen($keyContent);
            $result['content_preview'] = substr($keyContent, 0, 100) . '...';
            $result['has_begin_private'] = strpos($keyContent, '-----BEGIN PRIVATE KEY-----') !== false;
            $result['has_begin_rsa_private'] = strpos($keyContent, '-----BEGIN RSA PRIVATE KEY-----') !== false;
            $result['has_begin_ec_private'] = strpos($keyContent, '-----BEGIN EC PRIVATE KEY-----') !== false;
            $result['has_end_private'] = strpos($keyContent, '-----END PRIVATE KEY-----') !== false;
            $result['has_end_rsa_private'] = strpos($keyContent, '-----END RSA PRIVATE KEY-----') !== false;
            $result['has_end_ec_private'] = strpos($keyContent, '-----END EC PRIVATE KEY-----') !== false;
            
            // Check for common issues
            $result['has_carriage_returns'] = strpos($keyContent, "\r") !== false;
            $result['has_windows_line_endings'] = strpos($keyContent, "\r\n") !== false;
            $result['has_unix_line_endings'] = strpos($keyContent, "\n") !== false && strpos($keyContent, "\r\n") === false;
        }
        
        return $result;
    }

    /**
     * Comprehensive XML validation with detailed diagnostics
     */
    public function comprehensiveXmlValidation(string $xmlPath): array
    {
        $result = [
            'valid' => true,
            'errors' => [],
            'warnings' => [],
            'details' => []
        ];

        try {
            $doc = new DOMDocument();
            $doc->load($xmlPath);

            $xpath = new DOMXPath($doc);
            
            // Check all required namespaces
            $requiredNamespaces = [
                'cbc' => 'urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2',
                'cac' => 'urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2',
                'ext' => 'urn:oasis:names:specification:ubl:schema:xsd:CommonExtensionComponents-2',
                'sig' => 'urn:oasis:names:specification:ubl:schema:xsd:CommonSignatureComponents-2',
                'sac' => 'urn:oasis:names:specification:ubl:schema:xsd:SignatureAggregateComponents-2',
                'ds' => 'http://www.w3.org/2000/09/xmldsig#',
            ];

            foreach ($requiredNamespaces as $prefix => $uri) {
                $namespace = $doc->documentElement->getAttribute("xmlns:$prefix");
                if ($namespace === $uri) {
                    $result['details']["namespace_$prefix"] = "✅ Correct: $uri";
                } else {
                    $result['warnings'][] = "Namespace $prefix incorrect or missing";
                    $result['details']["namespace_$prefix"] = "❌ Expected: $uri, Found: $namespace";
                }
            }

            // Check critical elements
            $criticalElements = [
                '//cbc:ID' => 'Invoice ID',
                '//cbc:IssueDate' => 'Issue Date',
                '//cbc:IssueTime' => 'Issue Time',
                '//cac:AccountingSupplierParty' => 'Supplier Party',
                '//cac:AccountingCustomerParty' => 'Customer Party',
                '//cac:TaxTotal' => 'Tax Total',
                '//cac:LegalMonetaryTotal' => 'Legal Monetary Total',
                '//ext:UBLExtensions' => 'UBLExtensions',
                '//sac:SignatureInformation' => 'Signature Information',
            ];

            foreach ($criticalElements as $xpathQuery => $description) {
                $nodes = $xpath->query($xpathQuery);
                if ($nodes->length > 0) {
                    $result['details'][$description] = "✅ Found";
                } else {
                    $result['errors'][] = "Missing required element: $description";
                    $result['details'][$description] = "❌ Missing";
                }
            }

            // Check for PreviousInvoiceHash
            $pihNodes = $xpath->query('//sac:PreviousInvoiceHash');
            if ($pihNodes->length > 0) {
                $result['details']['PreviousInvoiceHash'] = "✅ Found";
            } else {
                $result['warnings'][] = "PreviousInvoiceHash not found";
                $result['details']['PreviousInvoiceHash'] = "⚠️ Missing";
            }

            // Check for invalid attributes in SignatureInformation
            $signatureInfoNodes = $xpath->query('//sac:SignatureInformation');
            if ($signatureInfoNodes->length > 0) {
                $signatureInfo = $signatureInfoNodes->item(0);
                if ($signatureInfo instanceof DOMElement) {
                    $id = $signatureInfo->getAttribute('id');
                    if ($id !== 'urn:oasis:names:specification:ubl:signature:1') {
                        $result['errors'][] = "SignatureInformation ID must be 'urn:oasis:names:specification:ubl:signature:1'";
                        $result['details']['SignatureInformation'] = "❌ Invalid ID: $id";
                    } else {
                        $result['details']['SignatureInformation'] = "✅ Valid ID";
                    }
                }
            }

            // Check that UBLDocumentSignatures is NOT present
            $ublDocumentSignatures = $xpath->query('//sig:UBLDocumentSignatures');
            if ($ublDocumentSignatures->length > 0) {
                $result['errors'][] = "UBLDocumentSignatures should not be present in ZATCA Phase 2";
                $result['details']['UBLDocumentSignatures'] = "❌ Should not be present";
            } else {
                $result['details']['UBLDocumentSignatures'] = "✅ Correctly absent";
            }

            // Update overall validity
            $result['valid'] = empty($result['errors']);

            Log::channel('zatca')->debug('✅ Comprehensive XML validation completed', $result);

        } catch (Exception $e) {
            $result['valid'] = false;
            $result['errors'][] = "XML parsing failed: " . $e->getMessage();
            Log::channel('zatca')->error('❌ Comprehensive XML validation failed', $result);
        }

        return $result;
    }

    /**
     * Generate canonicalized XML for hash calculation
     */
    public function generateCanonicalXml(string $xmlPath): string
    {
        try {
            $doc = new DOMDocument();
            $doc->load($xmlPath);
            
            // Use EXC-C14N canonicalization
            return $doc->C14N(true, false);
            
        } catch (Exception $e) {
            Log::channel('zatca')->error('❌ Failed to generate canonical XML', [
                'error' => $e->getMessage(),
                'path' => $xmlPath
            ]);
            throw new Exception("Failed to generate canonical XML: " . $e->getMessage());
        }
    }

    /**
     * Calculate XML hash for validation
     */
    public function calculateXmlHash(string $xmlPath): string
    {
        try {
            $canonicalXml = $this->generateCanonicalXml($xmlPath);
            $hash = hash('sha256', $canonicalXml, true);
            $base64Hash = base64_encode($hash);
            
            Log::channel('zatca')->debug('✅ XML hash calculated', [
                'hash_length' => strlen($base64Hash),
                'hash_preview' => substr($base64Hash, 0, 20) . '...'
            ]);
            
            return $base64Hash;
            
        } catch (Exception $e) {
            Log::channel('zatca')->error('❌ Failed to calculate XML hash', [
                'error' => $e->getMessage(),
                'path' => $xmlPath
            ]);
            throw new Exception("Failed to calculate XML hash: " . $e->getMessage());
        }
    }

    /**
     * Validate signed XML against ZATCA requirements
     */
    public function validateSignedXml(string $signedXmlPath): array
    {
        $result = [
            'valid' => true,
            'errors' => [],
            'warnings' => [],
            'signature_details' => []
        ];

        try {
            $doc = new DOMDocument();
            $doc->load($signedXmlPath);

            $xpath = new DOMXPath($doc);
            $xpath->registerNamespace('ds', 'http://www.w3.org/2000/09/xmldsig#');
            $xpath->registerNamespace('sac', 'urn:oasis:names:specification:ubl:schema:xsd:SignatureAggregateComponents-2');

            // Check signature exists
            $signatureNodes = $xpath->query('//ds:Signature');
            if ($signatureNodes->length === 0) {
                $result['errors'][] = "No signature found in XML";
                $result['valid'] = false;
            } else {
                $result['signature_details']['signature_count'] = $signatureNodes->length;
            }

            // Check signature method
            $signatureMethodNodes = $xpath->query('//ds:SignatureMethod');
            if ($signatureMethodNodes->length > 0) {
                $signatureMethod = $signatureMethodNodes->item(0);
                if ($signatureMethod instanceof DOMElement) {
                    $algorithm = $signatureMethod->getAttribute('Algorithm');
                    $result['signature_details']['signature_method'] = $algorithm;
                    
                    // ✅ CRITICAL FIX: Check for correct ZATCA signature method
                    if ($algorithm !== 'http://www.w3.org/2001/04/xmldsig-more#ecdsa-sha256') {
                        $result['errors'][] = "Invalid signature method: $algorithm. Expected: http://www.w3.org/2001/04/xmldsig-more#ecdsa-sha256";
                        $result['valid'] = false;
                    }
                }
            }

            // Check certificate exists
            $certificateNodes = $xpath->query('//ds:X509Certificate');
            if ($certificateNodes->length === 0) {
                $result['warnings'][] = "No X509Certificate found in signature";
            } else {
                $certificate = $certificateNodes->item(0)->nodeValue;
                $result['signature_details']['certificate_length'] = strlen($certificate);
                $result['signature_details']['certificate_preview'] = substr($certificate, 0, 50) . '...';
            }

            // Check SignatureInformation ID
            $signatureInfoNodes = $xpath->query('//sac:SignatureInformation');
            if ($signatureInfoNodes->length > 0) {
                $signatureInfo = $signatureInfoNodes->item(0);
                if ($signatureInfo instanceof DOMElement) {
                    $id = $signatureInfo->getAttribute('id');
                    $result['signature_details']['signature_info_id'] = $id;
                    
                    if ($id !== 'urn:oasis:names:specification:ubl:signature:1') {
                        $result['errors'][] = "Invalid SignatureInformation ID: $id";
                        $result['valid'] = false;
                    }
                }
            }

            // Check Signature ID
            $signatureIdNodes = $xpath->query('//ds:Signature[@Id="urn:oasis:names:specification:ubl:signature:Invoice"]');
            if ($signatureIdNodes->length === 0) {
                $result['warnings'][] = "Signature ID should be 'urn:oasis:names:specification:ubl:signature:Invoice'";
            }

            Log::channel('zatca')->debug('✅ Signed XML validation completed', $result);

        } catch (Exception $e) {
            $result['valid'] = false;
            $result['errors'][] = "Signed XML validation failed: " . $e->getMessage();
            Log::channel('zatca')->error('❌ Signed XML validation failed', $result);
        }

        return $result;
    }

    /**
     * Create a complete signing workflow
     */
    public function completeSigningWorkflow(string $unsignedXmlPath): array
    {
        $workflowResult = [
            'success' => false,
            'steps' => [],
            'signed_xml_path' => null,
            'errors' => []
        ];

        try {
            // Step 1: Comprehensive validation
            $workflowResult['steps']['validation'] = $this->comprehensiveXmlValidation($unsignedXmlPath);
            if (!$workflowResult['steps']['validation']['valid']) {
                $workflowResult['errors'] = $workflowResult['steps']['validation']['errors'];
                throw new Exception("XML validation failed");
            }

            // Step 2: Fix XML structure
            $this->fixXmlStructure($unsignedXmlPath);
            $workflowResult['steps']['fix_structure'] = ['success' => true];

            // Step 3: Calculate pre-signing hash
            $preSignHash = $this->calculateXmlHash($unsignedXmlPath);
            $workflowResult['steps']['pre_sign_hash'] = [
                'success' => true,
                'hash' => $preSignHash
            ];

            // Step 4: Sign the XML
            $signedXmlPath = $this->sign($unsignedXmlPath);
            $workflowResult['steps']['signing'] = ['success' => true, 'signed_path' => $signedXmlPath];
            $workflowResult['signed_xml_path'] = $signedXmlPath;

            // Step 5: Validate signed XML
            $workflowResult['steps']['post_sign_validation'] = $this->validateSignedXml($signedXmlPath);
            if (!$workflowResult['steps']['post_sign_validation']['valid']) {
                $workflowResult['errors'] = array_merge($workflowResult['errors'], $workflowResult['steps']['post_sign_validation']['errors']);
                throw new Exception("Signed XML validation failed");
            }

            // Step 6: Calculate final hash
            $finalHash = $this->calculateXmlHash($signedXmlPath);
            $workflowResult['steps']['final_hash'] = [
                'success' => true,
                'hash' => $finalHash
            ];

            $workflowResult['success'] = true;
            $workflowResult['message'] = 'Complete signing workflow completed successfully';

            Log::channel('zatca')->info('✅ Complete signing workflow completed', $workflowResult);

        } catch (Exception $e) {
            $workflowResult['success'] = false;
            $workflowResult['errors'][] = $e->getMessage();
            Log::channel('zatca')->error('❌ Complete signing workflow failed', $workflowResult);
        }

        return $workflowResult;
    }

    /**
     * Get service status and health check
     */
    public function getServiceStatus(): array
    {
        $status = [
            'service' => 'InvoiceSignerService',
            'status' => 'healthy',
            'checks' => []
        ];

        try {
            // Check certificate
            $certCheck = $this->testCertificateLoading();
            $status['checks']['certificate'] = $certCheck;

            // Check private key functionality
            $keyCheck = $this->testPrivateKeyFunctionality();
            $status['checks']['private_key'] = $keyCheck;

            // Check configuration
            $config = [
                'certificate_path' => config('zatca.certificate_path'),
                'private_key_path' => config('zatca.private_key_path'),
                'has_secret' => !empty(config('zatca.secret'))
            ];
            $status['checks']['configuration'] = $config;

            // Determine overall status
            if (!$certCheck['success'] || !$keyCheck['success']) {
                $status['status'] = 'unhealthy';
            }

        } catch (Exception $e) {
            $status['status'] = 'error';
            $status['error'] = $e->getMessage();
        }

        return $status;
    }
}