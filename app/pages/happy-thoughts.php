<?php

declare(strict_types=1);

require_once dirname(__DIR__, 2) . '/app/bootstrap.php';
require_request_method(['GET']);

$thoughts = all_thoughts();
$canManageThoughts = user_has_role(AUTH_ROLE_ADMIN);
$pageTitle = 'All Thoughts';
$pageDescription = 'Browse and manage your collection of happy moments.';
$currentPage = 'thoughts';
$libraryHeading = 'Thought Collection';
$libraryDescription = 'Browse, filter, and manage all your happy thoughts.';
$emptyMessage = 'No thoughts yet. Add your first happy moment!';
$serverSearch = '';
$pageProps = [
    'pageMode' => 'library',
    'heading' => $libraryHeading,
    'description' => $libraryDescription,
    'emptyMessage' => $emptyMessage,
    'serverSearch' => $serverSearch,
    'searchAction' => route_url('search'),
    'thoughts' => array_map(static fn (array $thought): array => [
        'id' => (int) $thought['id'],
        'title' => (string) $thought['title'],
        'author' => (string) $thought['author'],
        'category' => (string) $thought['category'],
        'moodScore' => (int) $thought['mood_score'],
        'thought' => (string) $thought['thought'],
        'imageUrl' => !empty($thought['image_path']) ? asset_url((string) $thought['image_path']) : null,
        'editUrl' => $canManageThoughts ? route_url('edit', ['id' => (int) $thought['id']]) : null,
        'deleteUrl' => $canManageThoughts ? route_url('delete', ['id' => (int) $thought['id']]) : null,
    ], $thoughts),
    'categories' => THOUGHT_CATEGORIES,
    'routes' => [
        'create' => $canManageThoughts ? route_url('create') : null,
        'search' => route_url('search'),
    ],
];

require project_root_path() . '/app/views/layout/header.php';
?>

<div data-thought-library-page></div>
<script id="thought-library-page-props" type="application/json"><?= page_props_json($pageProps) ?></script>

<?php require project_root_path() . '/app/views/layout/footer.php'; ?>
