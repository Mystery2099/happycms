<?php

declare(strict_types=1);

$requestPath = rawurldecode(parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?? '/');
$documentRoot = realpath(__DIR__);

if ($documentRoot === false) {
    http_response_code(500);
    exit('Server misconfiguration');
}

if (preg_match('#^/(?:storage|\.|node_modules)(?:/|$)#', $requestPath) === 1) {
    http_response_code(404);
    exit('Not Found');
}

function is_allowed_static_path(string $path): bool
{
    if ($path === '/favicon.ico') {
        return true;
    }

    return preg_match('#^/public/(?:assets|icons|images)/[A-Za-z0-9._/-]+$#', $path) === 1;
}

function is_allowed_php_entrypoint(string $path): bool
{
    static $allowedEntrypoints = [
        '/index.php',
        '/api/famous-thoughts.php',
    ];

    return in_array($path, $allowedEntrypoints, true);
}

$requestedFile = realpath($documentRoot . $requestPath);
if ($requestedFile !== false && str_starts_with($requestedFile, $documentRoot . DIRECTORY_SEPARATOR) && is_file($requestedFile)) {
    if (is_allowed_static_path($requestPath) || is_allowed_php_entrypoint($requestPath)) {
        return false;
    }

    http_response_code(404);
    exit('Not Found');
}

$candidateIndex = realpath($documentRoot . rtrim($requestPath, '/') . '/index.php');
if ($candidateIndex !== false && str_starts_with($candidateIndex, $documentRoot . DIRECTORY_SEPARATOR) && is_file($candidateIndex)) {
    require $candidateIndex;
    return true;
}

if ($requestPath !== '/') {
    header('Location: /', true, 302);
    exit;
}

require $documentRoot . '/index.php';
