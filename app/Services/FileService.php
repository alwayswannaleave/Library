<?php

namespace App\Services;

class FileService
{
    public function parseUploadedFile(array $file): ?string
    {
        if (!isset($file['tmp_name']) || empty($file['tmp_name'])) {
            return null;
        }

        $content = file_get_contents($file['tmp_name']);
        return $content !== false ? $content : null;
    }

    public function validateFile(array $file): array
    {
        if ($file['error'] !== UPLOAD_ERR_OK) {
            return ['error' => 'File upload failed'];
        }

        $maxSize = 5 * 1024 * 1024;
        if ($file['size'] > $maxSize) {
            return ['error' => 'File too large (max 5MB)'];
        }

        $allowedTypes = ['text/plain', 'application/octet-stream'];
        if (!in_array($file['type'], $allowedTypes)) {
            return ['error' => 'Only text files are allowed'];
        }

        return ['success' => true];
    }
}
