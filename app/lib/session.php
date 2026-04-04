<?php

declare(strict_types=1);

function boot_session(): void
{
    if (session_status() !== PHP_SESSION_NONE) {
        return;
    }

    ini_set('session.use_only_cookies', '1');
    ini_set('session.use_strict_mode', '1');
    ini_set('session.cookie_httponly', '1');

    if (!headers_sent()) {
        session_set_cookie_params([
            'httponly' => true,
            'samesite' => 'Lax',
            'secure' => !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off',
        ]);
    }

    $sessionPath = ini_get('session.save_path');
    if (!is_string($sessionPath) || $sessionPath === '' || !is_dir($sessionPath) || !is_writable($sessionPath)) {
        $fallbackSessionPath = sys_get_temp_dir() . '/happycms-sessions';
        if (!is_dir($fallbackSessionPath)) {
            mkdir($fallbackSessionPath, 0775, true);
        }

        session_save_path($fallbackSessionPath);
    }

    session_start();
}

function set_flash(string $type, string $message): void
{
    $_SESSION['flash'] = [
        'type' => $type,
        'message' => $message,
    ];
}

function get_flash(): ?array
{
    if (!isset($_SESSION['flash'])) {
        return null;
    }

    $flash = $_SESSION['flash'];
    unset($_SESSION['flash']);

    return $flash;
}
