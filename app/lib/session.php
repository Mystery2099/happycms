<?php

declare(strict_types=1);

function boot_session(): void
{
    /**
     * Start a hardened session with strict cookie settings and a writable
     * fallback save path for environments where the default session dir is unusable.
     */
    if (session_status() !== PHP_SESSION_NONE) {
        return;
    }

    $usesHttps = request_uses_https();

    ini_set('session.use_cookies', '1');
    ini_set('session.use_only_cookies', '1');
    ini_set('session.use_strict_mode', '1');
    ini_set('session.cookie_httponly', '1');

    if (!headers_sent()) {
        session_set_cookie_params([
            'path' => base_url_path() === '' ? '/' : base_url_path() . '/',
            'httponly' => true,
            'samesite' => 'Strict',
            'secure' => $usesHttps,
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

    if (!isset($_SESSION['session_initialized']) || $_SESSION['session_initialized'] !== true) {
        session_regenerate_id(true);
        $_SESSION['session_initialized'] = true;
    }
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
    /**
     * Flash messages are single-read by design so banners disappear after the
     * next rendered response instead of persisting across navigation.
     */
    if (!isset($_SESSION['flash'])) {
        return null;
    }

    $flash = $_SESSION['flash'];
    unset($_SESSION['flash']);

    return $flash;
}
