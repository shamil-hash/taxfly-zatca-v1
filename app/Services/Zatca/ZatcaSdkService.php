<?php

namespace App\Services\Zatca;

use Exception;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\File;

class ZatcaSdkService
{
    protected $sdkPath;
    protected $javaPath;
    protected $tempPath;
    protected $isWindows;

    public function __construct()
    {
        $config = config('zatca');
        $this->sdkPath = $config['sdk']['path'];
        $this->javaPath = $config['sdk']['java_path'];
        $this->tempPath = $config['sdk']['temp_path'];
        $this->isWindows = strtoupper(substr(PHP_OS, 0, 3)) === 'WIN';
        
        // Ensure temp directory exists
        if (!file_exists($this->tempPath)) {
            mkdir($this->tempPath, 0755, true);
        }
    }

    /**
     * Execute SDK command with Windows compatibility
     */
    protected function executeSdkCommand($command, $description = 'SDK command')
    {
        try {
            // Windows-specific command formatting
            if ($this->isWindows) {
                $fullCommand = "cd /D " . escapeshellarg($this->sdkPath) . " && " . 
                               $this->javaPath . " -jar Lib\\Java\\sdk-3.0.8-jar-with-dependencies.jar " . $command;
            } else {
                $fullCommand = "cd " . escapeshellarg($this->sdkPath) . " && " . 
                               $this->javaPath . " -jar Lib/Java/sdk-3.0.8-jar-with-dependencies.jar " . $command;
            }
            
            Log::info("Executing ZATCA SDK command: " . $fullCommand);
            
            exec($fullCommand, $output, $returnCode);
            
            if ($returnCode !== 0) {
                Log::error("ZATCA SDK {$description} failed", [
                    'command' => $fullCommand,
                    'output' => $output,
                    'return_code' => $returnCode
                ]);
                throw new Exception("{$description} failed: " . implode("\n", $output));
            }
            
            return [
                'success' => true,
                'output' => $output,
                'return_code' => $returnCode
            ];
            
        } catch (Exception $e) {
            Log::error("ZATCA SDK {$description} error", ['error' => $e->getMessage()]);
            throw $e;
        }
    }

    /**
     * Copy certificates to SDK directory (required for SDK to work)
     */
    public function setupSdkCertificates($privateKeyContent, $certificateContent)
    {
        try {
            $sdkCertPath = $this->sdkPath . ( $this->isWindows ? '\\Data\\Certificates\\' : '/Data/Certificates/' );
            
            // Format private key (remove headers/footers for SDK)
            $formattedPrivateKey = $this->formatPrivateKey($privateKeyContent);
            file_put_contents($sdkCertPath . 'ec-secp256k1-priv-key.pem', $formattedPrivateKey);
            
            // Format certificate (remove headers/footers for SDK)
            $formattedCertificate = $this->formatCertificate($certificateContent);
            file_put_contents($sdkCertPath . 'cert.pem', $formattedCertificate);
            
            Log::info('SDK certificates setup completed');
            return true;
            
        } catch (Exception $e) {
            Log::error('Failed to setup SDK certificates', ['error' => $e->getMessage()]);
            throw $e;
        }
    }

    /**
     * Format private key for SDK compatibility
     */
    protected function formatPrivateKey($keyContent)
    {
        // Remove headers/footers
        $keyContent = preg_replace('/-----BEGIN EC PRIVATE KEY-----/', '', $keyContent);
        $keyContent = preg_replace('/-----END EC PRIVATE KEY-----/', '', $keyContent);
        
        // Remove all whitespace
        $keyContent = preg_replace('/\s+/', '', $keyContent);
        
        return trim($keyContent);
    }

    /**
     * Format certificate for SDK compatibility
     */
    protected function formatCertificate($certContent)
    {
        // Remove headers/footers
        $certContent = preg_replace('/-----BEGIN CERTIFICATE-----/', '', $certContent);
        $certContent = preg_replace('/-----END CERTIFICATE-----/', '', $certContent);
        
        // Remove all whitespace and newlines
        $certContent = preg_replace('/\s+/', '', $certContent);
        
        return trim($certContent);
    }

    /**
     * Sign invoice using SDK
     */
    public function signInvoice($unsignedXml, $outputFile = null)
    {
        // Save XML to temporary file
        $tempFile = $this->tempPath . DIRECTORY_SEPARATOR . 'unsigned_' . time() . '.xml';
        file_put_contents($tempFile, $unsignedXml);
        
        // Generate output file name if not provided
        if (!$outputFile) {
            $outputFile = $this->tempPath . DIRECTORY_SEPARATOR . 'signed_' . time() . '.xml';
        }
        
        // Build SDK command with Windows path compatibility
        $tempFileEscaped = $this->escapePath($tempFile);
        $outputFileEscaped = $this->escapePath($outputFile);
        
        $command = "-sign -invoice " . $tempFileEscaped . 
                   " -signedInvoice " . $outputFileEscaped;
        
        $result = $this->executeSdkCommand($command, 'invoice signing');
        
        // Read signed content
        $signedXml = file_get_contents($outputFile);
        
        // Clean up temporary files
        unlink($tempFile);
        if (strpos($outputFile, $this->tempPath) === 0) {
            unlink($outputFile);
        }
        
        return $signedXml;
    }

    /**
     * Generate QR code using SDK
     */
    public function generateQrCode($signedXml)
    {
        // Save XML to temporary file
        $tempFile = $this->tempPath . DIRECTORY_SEPARATOR . 'qr_input_' . time() . '.xml';
        file_put_contents($tempFile, $signedXml);
        
        // Build SDK command
        $tempFileEscaped = $this->escapePath($tempFile);
        $command = "-qr -invoice " . $tempFileEscaped;
        
        $result = $this->executeSdkCommand($command, 'QR generation');
        
        // The QR code should be in the output
        $qrCode = end($result['output']);
        
        // Clean up temporary file
        unlink($tempFile);
        
        return $qrCode;
    }

    /**
     * Escape file paths for Windows compatibility
     */
    protected function escapePath($path)
    {
        if ($this->isWindows) {
            // Windows paths need different escaping
            return '"' . $path . '"';
        } else {
            return escapeshellarg($path);
        }
    }

    /**
     * Validate invoice using SDK
     */
    public function validateInvoice($signedXml)
    {
        // Save XML to temporary file
        $tempFile = $this->tempPath . DIRECTORY_SEPARATOR . 'validate_' . time() . '.xml';
        file_put_contents($tempFile, $signedXml);
        
        // Build SDK command
        $tempFileEscaped = $this->escapePath($tempFile);
        $command = "-validate -invoice " . $tempFileEscaped;
        
        $result = $this->executeSdkCommand($command, 'invoice validation');
        
        // Clean up temporary file
        unlink($tempFile);
        
        return [
            'valid' => $result['return_code'] === 0,
            'output' => implode("\n", $result['output']),
            'return_code' => $result['return_code']
        ];
    }

    /**
     * Generate CSR using SDK
     */
    public function generateCsr($configProperties, $privateKeyFile = null, $csrFile = null)
    {
        // Save config to temporary file
        $configFile = $this->tempPath . DIRECTORY_SEPARATOR . 'csr_config_' . time() . '.properties';
        file_put_contents($configFile, $configProperties);
        
        // Build command
        $configFileEscaped = $this->escapePath($configFile);
        $command = "-csr -csrConfig " . $configFileEscaped;
        
        if ($privateKeyFile) {
            $privateKeyFileEscaped = $this->escapePath($privateKeyFile);
            $command .= " -privateKey " . $privateKeyFileEscaped;
        }
        
        if ($csrFile) {
            $csrFileEscaped = $this->escapePath($csrFile);
            $command .= " -generatedCsr " . $csrFileEscaped;
        }
        
        $result = $this->executeSdkCommand($command, 'CSR generation');
        
        // Clean up temporary config file
        unlink($configFile);
        
        return $result;
    }

    /**
     * Generate invoice hash using SDK
     */
    public function generateInvoiceHash($invoiceXml)
    {
        // Save XML to temporary file
        $tempFile = $this->tempPath . DIRECTORY_SEPARATOR . 'hash_input_' . time() . '.xml';
        file_put_contents($tempFile, $invoiceXml);
        
        // Build SDK command
        $tempFileEscaped = $this->escapePath($tempFile);
        $command = "-generateHash -invoice " . $tempFileEscaped;
        
        $result = $this->executeSdkCommand($command, 'hash generation');
        
        // The hash should be in the output
        $hash = end($result['output']);
        
        // Clean up temporary file
        unlink($tempFile);
        
        return $hash;
    }
}