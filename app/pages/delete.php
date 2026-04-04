<?php

declare(strict_types=1);

require_once dirname(__DIR__, 2) . '/app/bootstrap.php';
require_request_method(['GET', 'POST']);

$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
$thought = find_thought($id);

if (!$thought) {
    set_flash('error', 'The selected thought no longer exists.');
    redirect_route('thoughts');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!is_same_origin_request() || !verify_csrf_token($_POST['csrf_token'] ?? null)) {
        set_flash('error', 'Your session expired. Refresh the page and try again.');
        redirect_route('delete', ['id' => $id]);
    }

    remove_thought($id);
    set_flash('success', 'Happy thought deleted successfully.');
    redirect_route('thoughts');
}

$pageTitle = 'Delete Thought | ' . h($thought['title']);
$pageDescription = 'Delete a happy thought record from the database.';
$currentPage = 'thoughts';
$pageProps = [
    'thought' => [
        'id' => $id,
        'title' => (string) $thought['title'],
        'author' => (string) $thought['author'],
        'category' => (string) $thought['category'],
        'moodScore' => (int) $thought['mood_score'],
        'thought' => (string) $thought['thought'],
    ],
    'formAction' => route_url('delete', ['id' => $id]),
    'cancelUrl' => route_url('thoughts'),
    'csrfToken' => csrf_token(),
];

require project_root_path() . '/app/views/layout/header.php';
?>

<div data-delete-thought-page></div>
<script id="delete-thought-page-props" type="application/json"><?= page_props_json($pageProps) ?></script>

<?php require project_root_path() . '/app/views/layout/footer.php'; ?>
