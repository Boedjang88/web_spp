<?php

namespace App\Services\Security;

use Illuminate\Http\UploadedFile;
use InvalidArgumentException;

class UploadSecurityGateway
{
    public const MAX_FILE_SIZE_BYTES = 10485760; // 10MB

    /**
     * Whitelist of allowed MIME types and their matching magic byte signatures.
     */
    protected const MAGIC_NUMBER_SIGNATURES = [
        'pdf' => [
            'mimes' => ['application/pdf'],
            'magic' => ["%PDF-"],
        ],
        'docx' => [
            'mimes' => [
                'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                'application/zip',
            ],
            'magic' => ["PK\x03\x04"],
        ],
        'zip' => [
            'mimes' => ['application/zip', 'application/x-zip-compressed'],
            'magic' => ["PK\x03\x04"],
        ],
        'png' => [
            'mimes' => ['image/png'],
            'magic' => ["\x89PNG\r\n\x1a\n"],
        ],
        'jpg' => [
            'mimes' => ['image/jpeg'],
            'magic' => ["\xFF\xD8\xFF"],
        ],
        'jpeg' => [
            'mimes' => ['image/jpeg'],
            'magic' => ["\xFF\xD8\xFF"],
        ],
    ];

    /**
     * Inspect file binary header and contents against malware/shell patterns.
     *
     * @param UploadedFile|string $file
     * @param array $allowedExtensions
     * @return array
     */
    public function validateFile($file, array $allowedExtensions = ['pdf', 'docx', 'zip']): array
    {
        $filePath = $file instanceof UploadedFile ? $file->getRealPath() : $file;
        $originalName = $file instanceof UploadedFile ? $file->getClientOriginalName() : basename($filePath);
        $fileSize = $file instanceof UploadedFile ? $file->getSize() : filesize($filePath);

        // 1. File Size Verification (Max 10MB)
        if ($fileSize > self::MAX_FILE_SIZE_BYTES) {
            return [
                'is_safe' => false,
                'error' => 'Ukuran file melebihi batas maksimum 10MB.',
                'size' => $fileSize,
            ];
        }

        if ($fileSize === 0) {
            return [
                'is_safe' => false,
                'error' => 'File kosong atau tidak dapat dibaca.',
                'size' => 0,
            ];
        }

        // 2. Extension Check
        $extension = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));
        if (!in_array($extension, $allowedExtensions)) {
            return [
                'is_safe' => false,
                'error' => "Ekstensi file .{$extension} tidak diizinkan. Ekstensi yang diizinkan: " . implode(', ', $allowedExtensions),
                'size' => $fileSize,
            ];
        }

        // 3. Read Header & Content for Magic Number Verification
        $handle = @fopen($filePath, 'rb');
        if (!$handle) {
            return [
                'is_safe' => false,
                'error' => 'Gagal membuka dan membaca header file.',
                'size' => $fileSize,
            ];
        }

        $headerBytes = fread($handle, 32);
        fclose($handle);

        $matchedMagic = false;
        $expectedSignatures = self::MAGIC_NUMBER_SIGNATURES[$extension] ?? null;

        if ($expectedSignatures) {
            foreach ($expectedSignatures['magic'] as $magicSig) {
                if (str_starts_with($headerBytes, $magicSig)) {
                    $matchedMagic = true;
                    break;
                }
            }
        }

        if (!$matchedMagic) {
            return [
                'is_safe' => false,
                'error' => "Header biner file (Magic Number) tidak sesuai dengan spesifikasi file .{$extension}.",
                'size' => $fileSize,
            ];
        }

        // 4. Anti-Webshell & Executable Injection Scanner
        $contentScan = file_get_contents($filePath, false, null, 0, min($fileSize, 204800)); // Read first 200KB
        $maliciousSignatures = [
            '<?php',
            '<?=',
            '<script language="php">',
            'base64_decode(',
            'gzinflate(',
            'eval(',
            'shell_exec(',
            'passthru(',
            'system(',
        ];

        foreach ($maliciousSignatures as $badSig) {
            if (stripos($contentScan, $badSig) !== false) {
                // If it's a ZIP or binary that coincidentally has characters, ensure it's not a clear PHP shell
                if (in_array($extension, ['docx', 'zip'])) {
                    // For zip files, specifically check if PHP tags exist in uncompressed or comment fields
                    if (stripos($contentScan, '<?php') !== false || stripos($contentScan, '<?=') !== false) {
                        return [
                            'is_safe' => false,
                            'error' => 'Terdeteksi kode script berbahaya (PHP Shell Injection) di dalam file.',
                            'size' => $fileSize,
                        ];
                    }
                } else {
                    return [
                        'is_safe' => false,
                        'error' => 'Terdeteksi payload script berbahaya di dalam berkas.',
                        'size' => $fileSize,
                    ];
                }
            }
        }

        $detectedMime = mime_content_type($filePath);

        return [
            'is_safe' => true,
            'extension' => $extension,
            'mime' => $detectedMime,
            'size' => $fileSize,
            'error' => null,
        ];
    }

    /**
     * Assert that a file is safe, throwing an exception if invalid.
     */
    public function assertSafeFile($file, array $allowedExtensions = ['pdf', 'docx', 'zip']): void
    {
        $result = $this->validateFile($file, $allowedExtensions);
        if (!$result['is_safe']) {
            throw new InvalidArgumentException($result['error']);
        }
    }

    /**
     * Inspect binary headers, scan for malware, and store securely with SHA-256 hash receipt.
     *
     * @param UploadedFile $file
     * @param string $directory
     * @param array $allowedExtensions
     * @return array
     */
    public function inspectAndStore(UploadedFile $file, string $directory = 'lms/submissions', array $allowedExtensions = ['pdf', 'docx', 'zip']): array
    {
        $this->assertSafeFile($file, $allowedExtensions);

        $authId = auth()->id() ?? 'guest';
        $microtime = microtime(true);
        $fileContents = file_get_contents($file->getRealPath());
        $hashReceipt = hash('sha256', $authId . '|' . $microtime . '|' . $fileContents);

        $storedPath = $file->store($directory, 'local');

        return [
            'path' => $storedPath,
            'hash_receipt' => $hashReceipt,
            'size' => $file->getSize(),
            'mime' => $file->getMimeType() ?? 'application/octet-stream',
            'original_name' => $file->getClientOriginalName(),
        ];
    }
}
