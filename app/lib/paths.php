<?php

declare(strict_types=1);

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
    return dirname(__DIR__, 2);
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

    return rtrim(substr($scriptName, 0, -strlen($relativeScriptPath)), '/');
}

function route_url(string $route, array $params = []): string
{
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
