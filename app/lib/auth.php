<?php

declare(strict_types=1);

const AUTH_ROLE_ADMIN = 'admin';

function auth_password_options(): array
{
    return ['cost' => 12];
}

function auth_default_admin_name(): string
{
    $name = trim((string) getenv('HAPPYCMS_ADMIN_NAME'));

    return $name !== '' ? $name : 'Happy Admin';
}

function auth_default_admin_email(): string
{
    $email = trim((string) getenv('HAPPYCMS_ADMIN_EMAIL'));

    return $email !== '' ? strtolower($email) : 'admin@happycms.local';
}

function auth_default_admin_password(): string
{
    $password = (string) getenv('HAPPYCMS_ADMIN_PASSWORD');

    return $password !== '' ? $password : 'ChangeMe123!';
}

function session_cookie_path(): string
{
    $basePath = base_url_path();

    return $basePath === '' ? '/' : $basePath . '/';
}

function session_cookie_is_secure(): bool
{
    return request_uses_https();
}

function remember_me_duration_seconds(): int
{
    return 60 * 60 * 24 * 30;
}

function session_cookie_options(bool $remember): array
{
    $options = [
        'path' => session_cookie_path(),
        'httponly' => true,
        'secure' => session_cookie_is_secure(),
        'samesite' => 'Strict',
    ];

    if ($remember) {
        $options['expires'] = time() + remember_me_duration_seconds();
    }

    return $options;
}

function authenticated_user_id(): ?int
{
    $userId = $_SESSION['auth_user_id'] ?? null;

    if (!is_int($userId) && !ctype_digit((string) $userId)) {
        return null;
    }

    $normalizedUserId = (int) $userId;

    return $normalizedUserId > 0 ? $normalizedUserId : null;
}

function current_user(): ?array
{
    static $user = null;
    static $resolved = false;

    if ($resolved) {
        return $user;
    }

    $resolved = true;
    $userId = authenticated_user_id();

    if ($userId === null) {
        return null;
    }

    $user = find_user_by_id($userId);
    if ($user === null || !($user['is_active'] ?? false)) {
        logout_user();
        $user = null;
    }

    return $user;
}

function is_logged_in(): bool
{
    return current_user() !== null;
}

function user_has_role(string $role): bool
{
    $user = current_user();

    return $user !== null && ($user['role'] ?? null) === $role;
}

function login_user(int $userId, bool $remember = false): void
{
    session_regenerate_id(true);
    $_SESSION['session_initialized'] = true;
    $_SESSION['auth_user_id'] = $userId;
    $_SESSION['auth_remember_me'] = $remember;

    setcookie(session_name(), session_id(), session_cookie_options($remember));
}

function logout_user(): void
{
    unset($_SESSION['auth_user_id'], $_SESSION['auth_remember_me']);

    setcookie(session_name(), '', [
        'expires' => time() - 3600,
        'path' => session_cookie_path(),
        'httponly' => true,
        'secure' => session_cookie_is_secure(),
        'samesite' => 'Strict',
    ]);

    session_regenerate_id(true);
    $_SESSION['session_initialized'] = true;
}

function authenticate_user(string $email, string $password): ?array
{
    $user = find_user_by_email($email);
    if ($user === null || !($user['is_active'] ?? false)) {
        return null;
    }

    $passwordHash = (string) ($user['password_hash'] ?? '');
    if ($passwordHash === '' || !password_verify($password, $passwordHash)) {
        return null;
    }

    if (password_needs_rehash($passwordHash, PASSWORD_DEFAULT, auth_password_options())) {
        rehash_user_password((int) $user['id'], $password);
        $user = find_user_by_id((int) $user['id']) ?? $user;
    }

    return $user;
}

function login_redirect_target(): string
{
    $redirect = trim((string) ($_GET['redirect'] ?? $_POST['redirect'] ?? ''));
    if ($redirect === '' || $redirect[0] !== '/') {
        return route_url('home');
    }

    if (str_contains($redirect, '://') || str_starts_with($redirect, '//')) {
        return route_url('home');
    }

    $basePath = base_url_path();
    if ($basePath !== '' && !str_starts_with($redirect, $basePath . '/') && $redirect !== $basePath) {
        return route_url('home');
    }

    return $redirect;
}

function login_route_url(array $params = []): string
{
    return route_url('login', $params);
}

function require_login(): void
{
    if (is_logged_in()) {
        return;
    }

    $path = rawurldecode(parse_url($_SERVER['REQUEST_URI'] ?? route_url('home'), PHP_URL_PATH) ?? route_url('home'));
    $query = (string) parse_url($_SERVER['REQUEST_URI'] ?? '', PHP_URL_QUERY);
    $redirectTarget = $query === '' ? $path : $path . '?' . $query;

    redirect(login_route_url(['redirect' => $redirectTarget]));
}

function require_role(string $role): void
{
    require_login();

    if (user_has_role($role)) {
        return;
    }

    http_response_code(403);
    exit('Forbidden');
}
