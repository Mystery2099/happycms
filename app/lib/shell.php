<?php

declare(strict_types=1);

function shell_routes(): array
{
    return [
        'home' => route_url('home'),
        'thoughts' => route_url('thoughts'),
        'create' => route_url('create'),
        'search' => route_url('search'),
        // Backend: Uncomment when login page is ready
        // 'login' => route_url('login'),
    ];
}

function shell_component_props(string $currentPage): array
{
    return [
        'currentPage' => $currentPage,
        'routes' => shell_routes(),
    ];
}

function flash_component_props(array $flash): array
{
    return [
        'type' => ($flash['type'] ?? '') === 'success' ? 'success' : 'error',
        'message' => (string) ($flash['message'] ?? ''),
    ];
}
