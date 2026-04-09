<?php

declare(strict_types=1);

function h(?string $value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

function page_props_json(array $props): string
{
    /**
     * Encode page props for inline JSON script tags with HTML-sensitive
     * characters escaped so the payload stays safe inside the document. Invalid
     * UTF-8 is substituted to avoid turning a render into a 500 for otherwise
     * serializable page data.
     */
    try {
        return json_encode(
            $props,
            JSON_HEX_TAG
            | JSON_HEX_AMP
            | JSON_HEX_APOS
            | JSON_HEX_QUOT
            | JSON_INVALID_UTF8_SUBSTITUTE
            | JSON_THROW_ON_ERROR
        );
    } catch (JsonException $exception) {
        log_internal_error($exception);

        return '{}';
    }
}

function project_root_path(): string
{
    return dirname(__DIR__, 2);
}

function project_relative_script_path(): string
{
    /**
     * Derive the executing script path relative to the project root so routing
     * still works when the app is served from a nested directory.
     */
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
    /**
     * Infer the deployment subdirectory from the current script path. The result
     * is empty for root installs and a stable prefix for nested installs.
     */
    $scriptName = (string) ($_SERVER['SCRIPT_NAME'] ?? '');
    $relativeScriptPath = project_relative_script_path();

    if ($relativeScriptPath === '' || !str_ends_with($scriptName, $relativeScriptPath)) {
        return '';
    }

    return rtrim(substr($scriptName, 0, -strlen($relativeScriptPath)), '/');
}

function route_url(string $route, array $params = []): string
{
    /**
     * Centralize route generation so PHP pages and the Svelte shell agree on
     * path structure regardless of where the application is mounted.
     */
    $routePaths = [
        'home' => '/',
        'create' => '/create/',
        'search' => '/search/',
        'thoughts' => '/thoughts/',
        'edit' => '/thoughts/edit/',
        'delete' => '/thoughts/delete/',
        'login' => '/login/',
        'logout' => '/logout/',
    ];

    $url = base_url_path() . ($routePaths[$route] ?? '/');
    $queryString = http_build_query($params);

    if ($url === '') {
        $url = '/';
    }

    return $queryString === '' ? $url : $url . '?' . $queryString;
}

function asset_url(string $path): string
{
    return base_url_path() . '/' . ltrim($path, '/');
}
