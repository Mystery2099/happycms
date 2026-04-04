<?php

declare(strict_types=1);

require_once dirname(__DIR__, 2) . '/app/bootstrap.php';
require_request_method(['GET', 'POST']);

$thoughtData = [
    'title' => '',
    'author' => '',
    'category' => 'Nature',
    'mood_score' => 3,
    'thought' => '',
    'image_path' => '',
];
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $formErrors = [];
    if (!is_same_origin_request()) {
        $formErrors['form'] = 'Invalid request origin. Refresh the page and try again.';
    }

    if (!verify_csrf_token($_POST['csrf_token'] ?? null)) {
        $formErrors['form'] = 'Your session expired. Refresh the page and try again.';
    }

    [$thoughtData, $errors] = validate_thought_input($_POST);
    $errors = array_merge($errors, $formErrors);

    if (!$errors) {
        create_thought($thoughtData);
        set_flash('success', 'Happy thought created successfully.');
        redirect_route('thoughts');
    }
}

$pageTitle = 'Add Happy Thought';
$pageDescription = 'Create a new happy thought with server-side validation.';
$currentPage = 'create';
$pageProps = [
    'mode' => 'create',
    'pageLabel' => 'Create',
    'title' => 'Add a new happy thought.',
    'description' => 'Share a moment of joy, gratitude, or positivity. Your thought will be stored in our database.',
    'formAction' => route_url('create'),
    'submitLabel' => 'Create Thought',
    'cancelUrl' => route_url('thoughts'),
    'thoughtData' => $thoughtData,
    'errors' => $errors,
    'categories' => THOUGHT_CATEGORIES,
    'csrfToken' => csrf_token(),
    'sideImageUrl' => asset_url('public/images/happy-sun.png'),
    'sideImageAlt' => 'Smiling sun illustration',
    'metadata' => null,
];

require project_root_path() . '/app/views/layout/header.php';
?>

<div data-thought-form-page></div>
<script id="thought-form-page-props" type="application/json"><?= page_props_json($pageProps) ?></script>

<?php require project_root_path() . '/app/views/layout/footer.php'; ?>
