<?php

declare(strict_types=1);

require_once dirname(__DIR__, 2) . '/app/bootstrap.php';
require_request_method(['GET', 'POST']);
require_role(AUTH_ROLE_ADMIN);

$currentUser = current_user();
$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
$existingThought = find_thought($id);

if (!$existingThought) {
    set_flash('error', 'The requested thought could not be found.');
    redirect_route('thoughts');
}

$thoughtData = [
    'title' => $existingThought['title'],
    'author' => $existingThought['author'],
    'category' => $existingThought['category'],
    'mood_score' => (int) $existingThought['mood_score'],
    'thought' => $existingThought['thought'],
    'image_path' => $existingThought['image_path'] ?? '',
];
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $formErrors = [];
    if (!is_same_origin_request()) {
        $formErrors['form'] = 'Invalid request origin. Refresh the page and try again.';
    } elseif (!verify_csrf_token($_POST['csrf_token'] ?? null)) {
        $formErrors['form'] = 'Your session expired. Refresh the page and try again.';
    }

    [$thoughtData, $errors] = validate_thought_input($_POST);
    $errors = $formErrors + $errors;

    if (!$errors) {
        update_thought($id, $thoughtData, isset($currentUser['id']) ? (int) $currentUser['id'] : null);
        set_flash('success', 'Happy thought updated successfully.');
        redirect_route('thoughts');
    }
}

$pageTitle = 'Edit Thought | ' . h($existingThought['title']);
$pageDescription = 'Edit an existing happy thought.';
$currentPage = 'thoughts';
$pageProps = [
    'mode' => 'edit',
    'pageLabel' => 'Edit Thought',
    'title' => (string) $existingThought['title'],
    'description' => 'Update the details below and save your changes.',
    'formAction' => route_url('edit', ['id' => $id]),
    'submitLabel' => 'Update Thought',
    'cancelUrl' => route_url('thoughts'),
    'backUrl' => route_url('thoughts'),
    'thoughtData' => $thoughtData,
    'errors' => $errors,
    'categories' => THOUGHT_CATEGORIES,
    'csrfToken' => csrf_token(),
    'sideImageUrl' => !empty($existingThought['image_path']) ? asset_url((string) $existingThought['image_path']) : null,
    'sideImageAlt' => !empty($existingThought['image_path']) ? (string) $existingThought['title'] : null,
    'metadata' => [
        'id' => $id,
        'createdAt' => date('F j, Y \\a\\t g:i A', strtotime($existingThought['created_at'])),
        'updatedAt' => date('F j, Y \\a\\t g:i A', strtotime($existingThought['updated_at'] ?? $existingThought['created_at'])),
        'category' => (string) $existingThought['category'],
        'moodScore' => (int) $existingThought['mood_score'],
        'createdBy' => !empty($existingThought['created_by_name'])
            ? (string) $existingThought['created_by_name']
            : (!empty($existingThought['created_by_email']) ? (string) $existingThought['created_by_email'] : null),
        'updatedBy' => !empty($existingThought['updated_by_name'])
            ? (string) $existingThought['updated_by_name']
            : (!empty($existingThought['updated_by_email']) ? (string) $existingThought['updated_by_email'] : null),
    ],
];

require project_root_path() . '/app/views/layout/header.php';
?>

<div data-thought-form-page></div>
<script id="thought-form-page-props" type="application/json"><?= page_props_json($pageProps) ?></script>

<?php require project_root_path() . '/app/views/layout/footer.php'; ?>
