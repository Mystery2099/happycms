<?php

declare(strict_types=1);

require_once dirname(__DIR__, 2) . '/app/bootstrap.php';
require_request_method(['GET']);

$query = normalized_search_query($_GET['q'] ?? '');
$thoughts = all_thoughts($query);
$canManageThoughts = user_has_role(AUTH_ROLE_ADMIN);
$pageTitle = 'Search Thoughts';
$pageDescription = 'Search the happy thoughts database.';
$currentPage = 'search';
$libraryHeading = $query === '' ? 'Search' : 'Results for "' . $query . '"';
$libraryDescription = $query === '' ? 'Enter a search term to find thoughts by title, author, category, or content.' : 'Found ' . count($thoughts) . ' matching thought' . (count($thoughts) === 1 ? '' : 's') . '.';
$emptyMessage = $query === '' ? 'Enter a search term above to get started.' : 'No thoughts found matching "' . $query . '".';
$serverSearch = $query;
$pageProps = [
    'pageMode' => 'search',
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
