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
$formAction = route_url('create');
$submitLabel = 'Create Thought';

require project_root_path() . '/app/views/layout/header.php';
?>

<section class="section-padding">
    <div class="max-w-6xl mx-auto px-6 lg:px-8">
        <div class="grid lg:grid-cols-[1fr_2fr] gap-12 lg:gap-16">
            <aside>
                <p class="text-sm font-medium text-stone uppercase tracking-widest mb-4">Create</p>
                <h1 class="font-display text-display-md text-ink mb-6">Add a new happy thought.</h1>
                <p class="text-stone leading-relaxed mb-8">Share a moment of joy, gratitude, or positivity. Your thought will be stored in our database.</p>
                
                <div class="aspect-square overflow-hidden bg-mist/30 flex items-center justify-center">
                    <img src="<?= h(asset_url('public/images/happy-sun.png')) ?>" alt="Smiling sun illustration" class="w-3/4 h-3/4 object-contain">
                </div>
            </aside>

            <div class="lg:pl-12 lg:border-l border-mist">
                <?php require project_root_path() . '/app/views/partials/thought-form.php'; ?>
            </div>
        </div>
    </div>
</section>

<?php require project_root_path() . '/app/views/layout/footer.php'; ?>
