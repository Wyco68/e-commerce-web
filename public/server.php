<?php

/**
 * Router for `php -S` on Render (and local). Trust TLS termination before Laravel boots.
 */
if (($_SERVER['HTTP_X_FORWARDED_PROTO'] ?? '') === 'https') {
    $_SERVER['HTTPS'] = 'on';
    $_SERVER['SERVER_PORT'] = '443';
}

$publicPath = __DIR__;
$uri = urldecode(parse_url($_SERVER['REQUEST_URI'] ?? '', PHP_URL_PATH) ?? '');

// Serve public-disk uploads when the storage symlink is absent (Render / php -S)
if (str_starts_with($uri, '/storage/')) {
    $relativePath = substr($uri, strlen('/storage/'));
    if ($relativePath !== '' && ! str_contains($relativePath, '..')) {
        $storageFile = storage_path('app/public/'.$relativePath);
        if (is_file($storageFile)) {
            $mime = mime_content_type($storageFile) ?: 'application/octet-stream';
            header('Content-Type: '.$mime);
            header('Content-Length: '.(string) filesize($storageFile));
            readfile($storageFile);

            return true;
        }
    }
}

// Serve built Vite assets and other static files (avoid HTML 404 → JS MIME type errors)
if ($uri !== '/' && $uri !== '' && is_file($publicPath.$uri)) {
    return false;
}

require __DIR__.'/../vendor/laravel/framework/src/Illuminate/Foundation/resources/server.php';
