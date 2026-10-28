<?php

declare(strict_types=1);

final class Upload
{
    public static function products(array $files): array
    {
        $saved = [];
        $normalized = self::normalize($files);

        foreach ($normalized as $file) {
            if (($file['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_NO_FILE) {
                continue;
            }
            $path = self::store($file, 'products');
            if ($path) {
                $saved[] = $path;
            }
        }

        return $saved;
    }

    public static function store(array $file, string $folder): ?string
    {
        if (($file['error'] ?? UPLOAD_ERR_OK) !== UPLOAD_ERR_OK) {
            return null;
        }

        $max = (int) config('upload.max_bytes', 4 * 1024 * 1024);
        if (($file['size'] ?? 0) > $max) {
            throw new RuntimeException('Image is too large. Maximum size is 4 MB.');
        }

        $tmp = $file['tmp_name'] ?? '';
        if ($tmp === '' || !is_uploaded_file($tmp)) {
            return null;
        }

        $info = @getimagesize($tmp);
        if ($info === false) {
            throw new RuntimeException('Only image files can be uploaded.');
        }

        $mime = $info['mime'] ?? '';
        $mimes = config('upload.mimes', []);
        if (!isset($mimes[$mime])) {
            throw new RuntimeException('Please upload a JPEG, PNG, WEBP, or GIF image.');
        }

        $dir = PUBLIC_PATH . '/uploads/' . $folder;
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

        $name = unique_token(16) . '.' . $mimes[$mime];
        $dest = $dir . '/' . $name;
        if (!move_uploaded_file($tmp, $dest)) {
            throw new RuntimeException('Could not save the uploaded image.');
        }

        return 'uploads/' . $folder . '/' . $name;
    }

    private static function normalize(array $files): array
    {
        if (!isset($files['name']) || !is_array($files['name'])) {
            return [$files];
        }

        $out = [];
        foreach ($files['name'] as $i => $name) {
            $out[] = [
                'name' => $name,
                'type' => $files['type'][$i] ?? '',
                'tmp_name' => $files['tmp_name'][$i] ?? '',
                'error' => $files['error'][$i] ?? UPLOAD_ERR_NO_FILE,
                'size' => $files['size'][$i] ?? 0,
            ];
        }
        return $out;
    }
}
