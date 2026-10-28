<?php
namespace App\Helpers;

use App\Core\Env;

class Uploader
{
    public static function uploadLeaveDocument(array $file): array
    {
        if (!isset($file['error']) || is_array($file['error'])) {
            return ['success' => false, 'error' => 'Invalid file parameter.'];
        }

        if ($file['error'] !== UPLOAD_ERR_OK) {
            return ['success' => false, 'error' => self::codeToMessage($file['error'])];
        }

        $maxSize = (int) Env::get('MAX_UPLOAD_SIZE_MB', 5) * 1024 * 1024;
        if ($file['size'] > $maxSize) {
            return ['success' => false, 'error' => 'File size exceeds maximum allowed size of 5MB.'];
        }

        // Validate MIME type securely using finfo
        $finfo = new \finfo(FILEINFO_MIME_TYPE);
        $mime = $finfo->file($file['tmp_name']);

        $allowedMimes = [
            'application/pdf' => 'pdf',
            'image/jpeg' => 'jpg',
            'image/png' => 'png',
        ];

        if (!array_key_exists($mime, $allowedMimes)) {
            return ['success' => false, 'error' => 'Invalid file format. Only PDF, JPG, and PNG are permitted.'];
        }

        // Validate extension
        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        if ($ext === 'jpeg') {
            $ext = 'jpg';
        }

        if (!in_array($ext, ['pdf', 'jpg', 'png'])) {
            return ['success' => false, 'error' => 'Invalid file extension.'];
        }

        // Generate safe unique filename
        $fileName = 'leave_' . bin2hex(random_bytes(16)) . '.' . $ext;
        $targetDir = dirname(__DIR__, 2) . '/public/uploads/leaves';

        if (!is_dir($targetDir)) {
            mkdir($targetDir, 0755, true);
        }

        $destination = $targetDir . '/' . $fileName;

        if (!move_uploaded_file($file['tmp_name'], $destination)) {
            return ['success' => false, 'error' => 'Failed to save uploaded file on server.'];
        }

        return [
            'success' => true,
            'filename' => $fileName,
            'path' => 'uploads/leaves/' . $fileName,
        ];
    }

    public static function saveBase64Image(string $base64Data, string $subfolder = 'faces'): ?string
    {
        if (preg_match('/^data:image\/(\w+);base64,/', $base64Data, $type)) {
            $data = substr($base64Data, strpos($base64Data, ',') + 1);
            $type = strtolower($type[1]); // jpg, png, jpeg

            if (!in_array($type, ['jpg', 'jpeg', 'png', 'webp'])) {
                return null;
            }

            $decoded = base64_decode($data);
            if ($decoded === false) {
                return null;
            }

            $fileName = 'face_' . bin2hex(random_bytes(16)) . '.' . ($type === 'jpeg' ? 'jpg' : $type);
            $targetDir = dirname(__DIR__, 2) . '/public/uploads/' . $subfolder;

            if (!is_dir($targetDir)) {
                mkdir($targetDir, 0755, true);
            }

            file_put_contents($targetDir . '/' . $fileName, $decoded);
            return 'uploads/' . $subfolder . '/' . $fileName;
        }

        return null;
    }

    private static function codeToMessage(int $code): string
    {
        return match ($code) {
            UPLOAD_ERR_INI_SIZE => 'The uploaded file exceeds the upload_max_filesize directive in php.ini.',
            UPLOAD_ERR_FORM_SIZE => 'The uploaded file exceeds the MAX_FILE_SIZE directive in HTML form.',
            UPLOAD_ERR_PARTIAL => 'The uploaded file was only partially uploaded.',
            UPLOAD_ERR_NO_FILE => 'No file was uploaded.',
            UPLOAD_ERR_NO_TMP_DIR => 'Missing a temporary folder on server.',
            UPLOAD_ERR_CANT_WRITE => 'Failed to write file to disk.',
            UPLOAD_ERR_EXTENSION => 'A PHP extension stopped the file upload.',
            default => 'Unknown upload error.',
        };
    }
}
