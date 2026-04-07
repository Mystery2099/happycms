<?php

declare(strict_types=1);

require_request_method(['GET']);

$stats = dashboard_stats();
$recentThoughts = recent_thoughts(4);
$canManageThoughts = user_has_role(AUTH_ROLE_ADMIN);
$homePageProps = [
    'stats' => [
        'total' => (int) $stats['total'],
        'categories' => (int) $stats['categories'],
        'highMood' => (int) $stats['high_mood'],
        'withImages' => (int) $stats['with_images'],
    ],
    'recentThoughts' => array_map(
        static fn (array $thought): array => [
            'id' => (int) $thought['id'],
            'title' => (string) $thought['title'],
            'author' => (string) $thought['author'],
            'category' => (string) $thought['category'],
            'moodScore' => (int) $thought['mood_score'],
            'thought' => (string) $thought['thought'],
            'editUrl' => $canManageThoughts ? route_url('edit', ['id' => (int) $thought['id']]) : null,
        ],
        $recentThoughts
    ),
    'routes' => [
        'create' => $canManageThoughts ? route_url('create') : null,
        'search' => route_url('search'),
        'thoughts' => route_url('thoughts'),
    ],
    'heroImageUrl' => asset_url('public/images/spring-hero.jpg'),
    'apiUrl' => asset_url('api/famous-thoughts.php'),
];
$homePagePropsJson = json_encode(
    $homePageProps,
    JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_THROW_ON_ERROR
);
$pageTitle = 'Happy Thoughts | Home';
$pageDescription = 'A collection of happy moments and positive reflections.';
$currentPage = 'home';

require project_root_path() . '/app/views/layout/header.php';
?>

<div data-home-page></div>
<script id="home-page-props" type="application/json">
<?= $homePagePropsJson ?>
</script>

<?php require project_root_path() . '/app/views/layout/footer.php'; ?>
