<?php

declare(strict_types=1);

function shell_routes(): array
{
    return [
        'home' => route_url('home'),
        'thoughts' => route_url('thoughts'),
        'create' => route_url('create'),
        'search' => route_url('search'),
        'login' => route_url('login'),
    ];
}

function shell_component_props(string $currentPage): array
{
    $user = current_user();

    return [
        'currentPage' => $currentPage,
        'routes' => shell_routes(),
        'isLoggedIn' => $user !== null,
        'isAdmin' => $user !== null && ($user['role'] ?? null) === AUTH_ROLE_ADMIN,
        'userName' => $user['name'] ?? '',
        'userEmail' => $user['email'] ?? '',
        'loginUrl' => route_url('login'),
        'logoutUrl' => route_url('logout'),
        'logoutCsrfToken' => csrf_token(),
    ];
}

function flash_component_props(array $flash): array
{
    return [
        'type' => ($flash['type'] ?? '') === 'success' ? 'success' : 'error',
        'message' => (string) ($flash['message'] ?? ''),
    ];
}
