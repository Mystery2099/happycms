<?php

declare(strict_types=1);

function require_request_method(array $allowedMethods): void
{
    $method = strtoupper((string) ($_SERVER['REQUEST_METHOD'] ?? 'GET'));
    $normalizedMethods = array_map(static fn (string $value): string => strtoupper($value), $allowedMethods);

    if (in_array($method, $normalizedMethods, true)) {
        return;
    }

    if (!headers_sent()) {
        http_response_code(405);
        header('Allow: ' . implode(', ', $normalizedMethods));
        header('Content-Type: text/plain; charset=utf-8');
    }

    exit('Method Not Allowed');
}

function expects_json_response(): bool
{
    return str_contains((string) ($_SERVER['HTTP_ACCEPT'] ?? ''), 'application/json');
}

function redirect(string $path): never
{
    header('Location: ' . $path);
    exit;
}

function redirect_route(string $route, array $params = []): never
{
    redirect(route_url($route, $params));
}
