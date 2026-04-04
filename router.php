<?php

declare(strict_types=1);

$requestPath = rawurldecode(parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?? '/');
$documentRoot = realpath(__DIR__);

if ($documentRoot === false) {
    http_response_code(500);
    exit('Server misconfiguration');
}

if (preg_match('#^/storage(?:/|$)#', $requestPath) === 1) {
    http_response_code(404);
    exit('Not Found');
}

$requestedFile = realpath($documentRoot . $requestPath);
if ($requestedFile !== false && str_starts_with($requestedFile, $documentRoot . DIRECTORY_SEPARATOR) && is_file($requestedFile)) {
    return false;
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
