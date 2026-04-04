<?php

declare(strict_types=1);

require_once __DIR__ . '/lib/database.php';

if (session_status() === PHP_SESSION_NONE) {
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

const THOUGHT_CATEGORIES = [
    'Nature',
    'Community',
    'Weather',
    'Growth',
    'Family',
    'Creativity',
    'Wellness',
];

const ALLOWED_IMAGE_EXTENSIONS = ['jpg', 'jpeg', 'png', 'gif', 'webp'];

function h(?string $value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

function page_props_json(array $props): string
{
    return json_encode(
        $props,
        JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_THROW_ON_ERROR
    );
}

function project_root_path(): string
{
    return dirname(__DIR__);
}

function project_relative_script_path(): string
{
    $scriptFilename = realpath((string) ($_SERVER['SCRIPT_FILENAME'] ?? ''));
    $projectRoot = realpath(project_root_path());

    if ($scriptFilename === false || $projectRoot === false) {
        return ltrim((string) ($_SERVER['SCRIPT_NAME'] ?? 'index.php'), '/');
    }

    if ($scriptFilename === $projectRoot) {
        return '';
    }

    $prefix = $projectRoot . DIRECTORY_SEPARATOR;
    if (!str_starts_with($scriptFilename, $prefix)) {
        return ltrim((string) ($_SERVER['SCRIPT_NAME'] ?? 'index.php'), '/');
    }

    return str_replace(DIRECTORY_SEPARATOR, '/', substr($scriptFilename, strlen($prefix)));
}

function base_url_path(): string
{
    $scriptName = (string) ($_SERVER['SCRIPT_NAME'] ?? '');
    $relativeScriptPath = project_relative_script_path();

    if ($relativeScriptPath === '' || !str_ends_with($scriptName, $relativeScriptPath)) {
        return '';
    }

    $basePath = substr($scriptName, 0, -strlen($relativeScriptPath));

    return rtrim($basePath, '/');
}

function route_url(string $route, array $params = []): string
{
    $basePath = base_url_path();
    $routePaths = [
        'home' => '/',
        'create' => '/create/',
        'search' => '/search/',
        'thoughts' => '/thoughts/',
        'edit' => '/thoughts/edit/',
        'delete' => '/thoughts/delete/',
    ];
    $path = $routePaths[$route] ?? '/';
    $queryString = http_build_query($params);
    $url = $basePath . $path;

    if ($url === '') {
        $url = '/';
    }

    return $queryString === '' ? $url : $url . '?' . $queryString;
}

function asset_url(string $path): string
{
    return base_url_path() . '/' . ltrim($path, '/');
}

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
    header('Permissions-Policy: accelerometer=(), camera=(), geolocation=(), gyroscope=(), magnetometer=(), microphone=(), payment=(), usb=()');
    header('Cross-Origin-Opener-Policy: same-origin');
    header('Cross-Origin-Resource-Policy: same-origin');
    header('Origin-Agent-Cluster: ?1');
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
        "upgrade-insecure-requests",
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

function require_request_method(array $allowedMethods): void
{
    $method = strtoupper((string) ($_SERVER['REQUEST_METHOD'] ?? 'GET'));
    $allowedMethods = array_map(static fn (string $value): string => strtoupper($value), $allowedMethods);

    if (in_array($method, $allowedMethods, true)) {
        return;
    }

    if (!headers_sent()) {
        http_response_code(405);
        header('Allow: ' . implode(', ', $allowedMethods));
        header('Content-Type: text/plain; charset=utf-8');
    }

    exit('Method Not Allowed');
}

function expects_json_response(): bool
{
    $accept = $_SERVER['HTTP_ACCEPT'] ?? '';

    return str_contains((string) $accept, 'application/json');
}

function log_internal_error(Throwable $exception): void
{
    error_log(sprintf(
        '[happycms] %s in %s:%d',
        $exception->getMessage(),
        $exception->getFile(),
        $exception->getLine()
    ));
}

function render_error_response(int $statusCode = 500): never
{
    http_response_code($statusCode);

    if (expects_json_response()) {
        send_json_security_headers();
        header('Content-Type: application/json; charset=utf-8');

        echo json_encode([
            'success' => false,
            'error' => 'A server error occurred.',
        ], JSON_UNESCAPED_SLASHES | JSON_THROW_ON_ERROR);

        exit;
    }

    send_page_security_headers();

    echo '<!DOCTYPE html><html lang="en"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0"><title>Server Error</title></head><body><main><h1>Something went wrong.</h1><p>The request could not be completed. Please refresh the page and try again.</p></main></body></html>';
    exit;
}

function install_error_handlers(): void
{
    static $installed = false;

    if ($installed) {
        return;
    }

    set_exception_handler(static function (Throwable $exception): void {
        log_internal_error($exception);
        render_error_response(500);
    });

    register_shutdown_function(static function (): void {
        $error = error_get_last();
        if (!is_array($error)) {
            return;
        }

        $fatalTypes = [E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR];
        if (!in_array($error['type'] ?? null, $fatalTypes, true)) {
            return;
        }

        error_log(sprintf(
            '[happycms] Fatal error: %s in %s:%d',
            $error['message'] ?? 'Unknown fatal error',
            $error['file'] ?? 'unknown',
            $error['line'] ?? 0
        ));

        render_error_response(500);
    });

    $installed = true;
}

function normalized_text_input(mixed $value, int $maxLength): string
{
    $text = trim((string) $value);
    $text = preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]/u', '', $text) ?? '';

    // Check for mbstring extension, fall back to strlen/substr if not available
    if (function_exists('mb_strlen') && function_exists('mb_substr')) {
        if (mb_strlen($text) > $maxLength) {
            $text = mb_substr($text, 0, $maxLength);
        }
    } else {
        if (strlen($text) > $maxLength) {
            $text = substr($text, 0, $maxLength);
        }
    }

    return $text;
}

function text_length(string $value): int
{
    if (function_exists('mb_strlen')) {
        return mb_strlen($value);
    }

    return strlen($value);
}

function normalized_search_query(mixed $value): string
{
    return normalized_text_input($value, 100);
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

function redirect(string $path): never
{
    header('Location: ' . $path);
    exit;
}

function redirect_route(string $route, array $params = []): never
{
    redirect(route_url($route, $params));
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

function normalize_local_image_path(?string $path): ?string
{
    $path = trim((string) $path);
    if ($path === '') {
        return null;
    }

    $normalizedPath = str_replace('\\', '/', ltrim($path, '/'));
    if (!preg_match('#^(images|public/assets)/[A-Za-z0-9._/-]+$#', $normalizedPath)) {
        return null;
    }

    if (str_contains($normalizedPath, '../') || str_contains($normalizedPath, '/..')) {
        return null;
    }

    $extension = strtolower(pathinfo($normalizedPath, PATHINFO_EXTENSION));
    if ($extension === '' || !in_array($extension, ALLOWED_IMAGE_EXTENSIONS, true)) {
        return null;
    }

    $resolvedPath = realpath(project_root_path() . '/' . $normalizedPath);
    if ($resolvedPath === false) {
        return null;
    }

    $projectRoot = project_root_path();
    if (!str_starts_with($resolvedPath, $projectRoot . DIRECTORY_SEPARATOR)) {
        return null;
    }

    if (!is_file($resolvedPath) || !is_readable($resolvedPath)) {
        return null;
    }

    return str_replace(DIRECTORY_SEPARATOR, '/', substr($resolvedPath, strlen($projectRoot) + 1));
}

function validate_thought_input(array $input): array
{
    $data = [
        'title' => normalized_text_input($input['title'] ?? '', 80),
        'author' => normalized_text_input($input['author'] ?? '', 60),
        'category' => normalized_text_input($input['category'] ?? 'Nature', 30),
        'mood_score' => (int) ($input['mood_score'] ?? 3),
        'thought' => normalized_text_input($input['thought'] ?? '', 400),
        'image_path' => normalized_text_input($input['image_path'] ?? '', 255),
    ];

    $errors = [];

    if ($data['title'] === '' || text_length($data['title']) < 3) {
        $errors['title'] = 'Use a title with at least 3 characters.';
    }

    if (text_length($data['title']) > 80) {
        $errors['title'] = 'Title must stay under 80 characters.';
    }

    if ($data['author'] === '' || text_length($data['author']) < 2) {
        $errors['author'] = 'Author is required.';
    }

    if (text_length($data['author']) > 60) {
        $errors['author'] = 'Author must stay under 60 characters.';
    }

    if (!in_array($data['category'], THOUGHT_CATEGORIES, true)) {
        $errors['category'] = 'Choose one of the listed categories.';
    }

    if ($data['mood_score'] < 1 || $data['mood_score'] > 5) {
        $errors['mood_score'] = 'Mood score must be between 1 and 5.';
    }

    if ($data['thought'] === '' || text_length($data['thought']) < 12) {
        $errors['thought'] = 'Thought text must be at least 12 characters.';
    }

    if (text_length($data['thought']) > 400) {
        $errors['thought'] = 'Thought text must stay under 400 characters.';
    }

    $normalizedImagePath = normalize_local_image_path($data['image_path']);
    if ($data['image_path'] !== '' && $normalizedImagePath === null) {
        $errors['image_path'] = 'Use an existing local image in images/ or public/assets/ with a standard image extension.';
    }

    $data['image_path'] = $normalizedImagePath;

    return [$data, $errors];
}

function all_thoughts(?string $search = null): array
{
    $pdo = get_pdo();
    $search = trim((string) $search);

    if ($search === '') {
        return $pdo->query('SELECT * FROM happy_thoughts ORDER BY created_at DESC, id DESC')->fetchAll();
    }

    $statement = $pdo->prepare(
        'SELECT * FROM happy_thoughts
         WHERE title LIKE :search
            OR author LIKE :search
            OR category LIKE :search
            OR thought LIKE :search
         ORDER BY created_at DESC, id DESC'
    );
    $statement->execute(['search' => '%' . $search . '%']);

    return $statement->fetchAll();
}

function recent_thoughts(int $limit = 3): array
{
    $statement = get_pdo()->prepare('SELECT * FROM happy_thoughts ORDER BY created_at DESC, id DESC LIMIT :limit');
    $statement->bindValue(':limit', $limit, PDO::PARAM_INT);
    $statement->execute();

    return $statement->fetchAll();
}

function find_thought(int $id): ?array
{
    $statement = get_pdo()->prepare('SELECT * FROM happy_thoughts WHERE id = :id LIMIT 1');
    $statement->execute(['id' => $id]);
    $thought = $statement->fetch();

    return $thought ?: null;
}

function create_thought(array $data): int
{
    $statement = get_pdo()->prepare(
        'INSERT INTO happy_thoughts (title, author, category, mood_score, thought, image_path)
         VALUES (:title, :author, :category, :mood_score, :thought, :image_path)'
    );
    $statement->execute($data);

    return (int) get_pdo()->lastInsertId();
}

function update_thought(int $id, array $data): void
{
    $data['id'] = $id;
    $statement = get_pdo()->prepare(
        'UPDATE happy_thoughts
         SET title = :title,
             author = :author,
             category = :category,
             mood_score = :mood_score,
             thought = :thought,
             image_path = :image_path,
             updated_at = CURRENT_TIMESTAMP
         WHERE id = :id'
    );
    $statement->execute($data);
}

function remove_thought(int $id): void
{
    $statement = get_pdo()->prepare('DELETE FROM happy_thoughts WHERE id = :id');
    $statement->execute(['id' => $id]);
}

function dashboard_stats(): array
{
    $pdo = get_pdo();

    return [
        'total' => (int) $pdo->query('SELECT COUNT(*) FROM happy_thoughts')->fetchColumn(),
        'categories' => (int) $pdo->query('SELECT COUNT(DISTINCT category) FROM happy_thoughts')->fetchColumn(),
        'high_mood' => (int) $pdo->query('SELECT COUNT(*) FROM happy_thoughts WHERE mood_score >= 4')->fetchColumn(),
        'with_images' => (int) $pdo->query('SELECT COUNT(*) FROM happy_thoughts WHERE image_path IS NOT NULL')->fetchColumn(),
    ];
}

function famous_thoughts_file_path(): string
{
    return app_data_root_path() . '/database/famous-thoughts.txt';
}

function load_famous_thoughts(): array
{
    $candidateFiles = [
        famous_thoughts_file_path(),
        project_root_path() . '/storage/database/famous-thoughts.txt',
    ];

    $file = null;
    foreach ($candidateFiles as $candidateFile) {
        if (is_file($candidateFile)) {
            $file = $candidateFile;
            break;
        }
    }

    if ($file === null) {
        return [];
    }

    $lines = file($file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    $thoughts = [];

    foreach ($lines as $line) {
        [$author, $quote, $category] = array_pad(array_map('trim', explode('|', $line)), 3, '');
        if ($author === '' || $quote === '') {
            continue;
        }

        $thoughts[] = [
            'author' => trim($author, '"'),
            'quote' => trim($quote, '"'),
            'category' => trim($category, '"'),
        ];
    }

    return $thoughts;
}

install_error_handlers();
