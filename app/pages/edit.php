<?php

declare(strict_types=1);

require_once dirname(__DIR__, 2) . '/app/bootstrap.php';
require_request_method(['GET', 'POST']);

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
    }

    if (!verify_csrf_token($_POST['csrf_token'] ?? null)) {
        $formErrors['form'] = 'Your session expired. Refresh the page and try again.';
    }

    [$thoughtData, $errors] = validate_thought_input($_POST);
    $errors = array_merge($errors, $formErrors);

    if (!$errors) {
        update_thought($id, $thoughtData);
        set_flash('success', 'Happy thought updated successfully.');
        redirect_route('thoughts');
    }
}

$pageTitle = 'Edit Thought | ' . h($existingThought['title']);
$pageDescription = 'Edit an existing happy thought.';
$currentPage = 'thoughts';
$formAction = route_url('edit', ['id' => $id]);
$submitLabel = 'Update Thought';

require project_root_path() . '/app/views/layout/header.php';
?>

<section class="section-padding">
    <div class="max-w-6xl mx-auto px-6 lg:px-8">
        <!-- Back to collection -->
        <div class="mb-8">
            <a href="<?= h(route_url('thoughts')) ?>" class="text-sm text-stone hover:text-ink transition-colors inline-flex items-center gap-2">
                <i data-lucide="chevron-left" class="w-4 h-4"></i>
                Back to collection
            </a>
        </div>

        <!-- Record metadata -->
        <div class="border-b border-mist pb-6 mb-8">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div>
                    <p class="text-sm text-stone inline-flex items-center gap-2">
                        <i data-lucide="hash" class="w-4 h-4"></i>
                        Editing thought #<?= $id ?>
                    </p>
                    <p class="text-sm text-stone inline-flex items-center gap-2 mt-1">
                        <i data-lucide="calendar-days" class="w-4 h-4"></i>
                        Last updated: <?= date('F j, Y', strtotime($existingThought['updated_at'] ?? $existingThought['created_at'])) ?>
                    </p>
                </div>
                <span class="inline-flex items-center px-3 py-1 text-xs font-medium bg-mist/50 text-stone rounded-full">
                    <i data-lucide="tag" class="w-3 h-3 mr-1.5"></i>
                    <?= h($existingThought['category']) ?> • <?= str_repeat('★', (int) $existingThought['mood_score']) ?>
                </span>
            </div>
        </div>

        <div class="grid lg:grid-cols-[1fr_2fr] gap-12 lg:gap-16">
            <aside>
                <p class="text-sm font-medium text-stone uppercase tracking-widest mb-4">Edit Thought</p>
                <h1 class="font-display text-display-md text-ink mb-6 inline-flex items-center gap-3">
                    <i data-lucide="pencil" class="w-6 h-6 text-coral"></i>
                    <?= h($existingThought['title']) ?>
                </h1>
                <p class="text-stone leading-relaxed mb-8">Update the details below and save your changes.</p>
                
                <?php if (!empty($existingThought['image_path'])): ?>
                    <div class="aspect-[4/3] overflow-hidden">
                        <img src="<?= h(asset_url((string) $existingThought['image_path'])) ?>" alt="<?= h($existingThought['title']) ?>" class="w-full h-full object-cover">
                    </div>
                <?php endif; ?>
            </aside>

            <div class="lg:pl-12 lg:border-l border-mist">
                <?php require project_root_path() . '/app/views/partials/thought-form.php'; ?>
            </div>
        </div>
    </div>
</section>

<?php require project_root_path() . '/app/views/layout/footer.php'; ?>
