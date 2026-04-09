<?php

declare(strict_types=1);

require_once __DIR__ . '/../app/bootstrap.php';

$_SERVER['HTTP_ACCEPT'] = 'application/json';
$retryAfter = consume_rate_limit('api-famous-thoughts', 60, 60);

if (strtoupper((string) ($_SERVER['REQUEST_METHOD'] ?? 'GET')) !== 'GET') {
    send_json_security_headers();
    http_response_code(405);
    header('Allow: GET');
    header('Content-Type: application/json; charset=utf-8');

    try {
        echo json_encode([
            'success' => false,
            'error' => 'Method Not Allowed',
        ], JSON_UNESCAPED_SLASHES | JSON_THROW_ON_ERROR);
    } catch (JsonException $exception) {
        log_internal_error($exception);
        echo '{"success":false,"error":"Method Not Allowed"}';
    }

    exit;
}

if ($retryAfter !== null) {
    send_json_security_headers();
    http_response_code(429);
    header('Retry-After: ' . $retryAfter);
    header('Content-Type: application/json; charset=utf-8');

    try {
        echo json_encode([
            'success' => false,
            'error' => 'Too Many Requests',
            'retryAfter' => $retryAfter,
        ], JSON_UNESCAPED_SLASHES | JSON_THROW_ON_ERROR);
    } catch (JsonException $exception) {
        log_internal_error($exception);
        echo '{"success":false,"error":"Too Many Requests"}';
    }

    exit;
}

send_json_security_headers();
header('Content-Type: application/json; charset=utf-8');

$quotes = load_famous_thoughts();

try {
    echo json_encode([
        'success' => true,
        'count' => count($quotes),
        'quotes' => $quotes,
    ], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_THROW_ON_ERROR);
} catch (JsonException $exception) {
    log_internal_error($exception);
    http_response_code(500);
    echo '{"success":false,"error":"A server error occurred."}';
}
