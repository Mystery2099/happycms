<?php

declare(strict_types=1);

function csp_nonce(): string
{
    static $nonce = null;

    if (!is_string($nonce)) {
        $nonce = bin2hex(random_bytes(16));
    }

    return $nonce;
}

function send_common_security_headers(): void
{
    if (headers_sent()) {
        return;
    }

    header_remove('X-Powered-By');
    header('Referrer-Policy: strict-origin-when-cross-origin');
    header('X-Content-Type-Options: nosniff');
    header('X-Frame-Options: DENY');
    header('X-Permitted-Cross-Domain-Policies: none');
    header('Permissions-Policy: accelerometer=(), camera=(), geolocation=(), gyroscope=(), magnetometer=(), microphone=(), payment=(), usb=()');
    header('Cross-Origin-Opener-Policy: same-origin');
    header('Cross-Origin-Resource-Policy: same-origin');
    header('Origin-Agent-Cluster: ?1');

    if (request_uses_https()) {
        header('Strict-Transport-Security: max-age=31536000; includeSubDomains');
    }
}

function send_page_security_headers(): void
{
    send_common_security_headers();

    if (headers_sent()) {
        return;
    }

    $nonce = csp_nonce();
    $policy = [
        "default-src 'self'",
        "base-uri 'self'",
        "form-action 'self'",
        "frame-ancestors 'none'",
        "object-src 'none'",
        "img-src 'self' data:",
        "font-src 'self' https://fonts.gstatic.com",
        "style-src 'self' 'nonce-{$nonce}' https://fonts.googleapis.com",
        "script-src 'self' 'nonce-{$nonce}'",
        "connect-src 'self'",
        'upgrade-insecure-requests',
    ];

    header('Content-Security-Policy: ' . implode('; ', $policy));
    header('Content-Type: text/html; charset=utf-8');
    header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
    header('Pragma: no-cache');
}

function send_json_security_headers(): void
{
    send_common_security_headers();

    if (headers_sent()) {
        return;
    }

    header("Content-Security-Policy: default-src 'none'; frame-ancestors 'none'; base-uri 'none'");
    header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
    header('Pragma: no-cache');
}

function current_origin(): ?string
{
    $hostHeader = $_SERVER['HTTP_HOST'] ?? '';
    if (!is_string($hostHeader) || trim($hostHeader) === '') {
        return null;
    }

    $scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
    $parsedHost = parse_url('http://' . trim($hostHeader));
    if (!is_array($parsedHost) || !isset($parsedHost['host'])) {
        return null;
    }

    $origin = $scheme . '://' . $parsedHost['host'];
    if (isset($parsedHost['port'])) {
        $origin .= ':' . $parsedHost['port'];
    }

    return $origin;
}

function request_uses_https(): bool
{
    return !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off';
}

function is_same_origin_request(): bool
{
    $expectedOrigin = current_origin();
    if ($expectedOrigin === null) {
        return false;
    }

    $origin = $_SERVER['HTTP_ORIGIN'] ?? null;
    if (is_string($origin) && $origin !== '') {
        return rtrim($origin, '/') === $expectedOrigin;
    }

    $referer = $_SERVER['HTTP_REFERER'] ?? null;
    if (!is_string($referer) || $referer === '') {
        return false;
    }

    $parts = parse_url($referer);
    if (!is_array($parts) || !isset($parts['scheme'], $parts['host'])) {
        return false;
    }

    $refererOrigin = $parts['scheme'] . '://' . $parts['host'];
    if (isset($parts['port'])) {
        $refererOrigin .= ':' . $parts['port'];
    }

    return $refererOrigin === $expectedOrigin;
}

function csrf_token(): string
{
    if (!isset($_SESSION['csrf_token']) || !is_string($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }

    return $_SESSION['csrf_token'];
}

function verify_csrf_token(?string $token): bool
{
    return is_string($token)
        && isset($_SESSION['csrf_token'])
        && is_string($_SESSION['csrf_token'])
        && hash_equals($_SESSION['csrf_token'], $token);
}
