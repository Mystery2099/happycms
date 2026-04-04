<?php

declare(strict_types=1);

require_once __DIR__ . '/../app/bootstrap.php';

require_request_method(['GET']);
send_json_security_headers();

header('Content-Type: application/json; charset=utf-8');

$quotes = load_famous_thoughts();

echo json_encode([
    'success' => true,
    'count' => count($quotes),
    'quotes' => $quotes,
], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_THROW_ON_ERROR);
