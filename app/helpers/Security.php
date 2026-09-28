<?php
namespace App\Helpers;

class Security {
    public static function clean(mixed $data): mixed {
        if (is_array($data)) {
            foreach ($data as $k => $v) {
                $data[$k] = self::clean($v);
            }
            return $data;
        }
        if (is_string($data)) {
            return htmlspecialchars(trim($data), ENT_QUOTES, 'UTF-8');
        }
        return $data;
    }

    public static function cleanRichText(string $html): string {
        // Allow safe HTML tags for story description & chapter content
        $allowedTags = '<p><br><b><strong><i><em><u><h1><h2><h3><h4><h5><h6><blockquote><ul><ol><li><span><hr>';
        return strip_tags(trim($html), $allowedTags);
    }

    public static function handleUpload(array $file, string $subFolder = 'covers', array $allowedMimes = ['image/jpeg', 'image/png', 'image/webp']): ?string {
        if (!isset($file['error']) || $file['error'] !== UPLOAD_ERR_OK) {
            return null;
        }

        $finfo = new \finfo(FILEINFO_MIME_TYPE);
        $mime = $finfo->file($file['tmp_name']);

        if (!in_array($mime, $allowedMimes)) {
            return null;
        }

        // Limit 5MB
        if ($file['size'] > 5 * 1024 * 1024) {
            return null;
        }

        $ext = match ($mime) {
            'image/jpeg' => 'jpg',
            'image/png'  => 'png',
            'image/webp' => 'webp',
            default      => 'jpg'
        };

        $targetDir = UPLOAD_PATH . '/' . $subFolder;
        if (!is_dir($targetDir)) {
            mkdir($targetDir, 0777, true);
        }

        $fileName = uniqid('img_', true) . '.' . $ext;
        $targetFile = $targetDir . '/' . $fileName;

        if (move_uploaded_file($file['tmp_name'], $targetFile)) {
            return $fileName;
        }

        return null;
    }
}
