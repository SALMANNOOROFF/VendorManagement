<?php
class FileUpload {
    private array $allowedTypes = ['application/pdf', 'image/jpeg', 'image/png', 'image/jpg'];
    private int $maxSize = 5242880; // 5MB

    public function validate(array $file): array {
        if ($file['error'] !== UPLOAD_ERR_OK) {
            return ['valid' => false, 'error' => 'Upload failed.'];
        }
        if ($file['size'] > $this->maxSize) {
            return ['valid' => false, 'error' => 'File too large. Max 5MB.'];
        }
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mime = finfo_file($finfo, $file['tmp_name']);
        finfo_close($finfo);
        if (!in_array($mime, $this->allowedTypes)) {
            return ['valid' => false, 'error' => 'Only PDF, JPG, PNG allowed.'];
        }
        return ['valid' => true];
    }

    public function move(array $file, string $destination): ?string {
        if (!is_dir($destination)) {
            mkdir($destination, 0755, true);
        }
        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        $filename = uniqid('doc_', true) . '.' . $ext;
        $path = $destination . '/' . $filename;
        if (move_uploaded_file($file['tmp_name'], $path)) {
            return $path;
        }
        return null;
    }
}
