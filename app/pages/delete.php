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

require project_root_path() . '/app/views/layout/header.php';
?>

<section class="section-padding">
    <div class="max-w-2xl mx-auto px-6 lg:px-8">
        <!-- Warning banner -->
        <div class="bg-coral/10 border-l-4 border-coral p-4 mb-8">
            <div class="flex items-start gap-3">
                <i data-lucide="alert-triangle" class="w-5 h-5 text-coral flex-shrink-0 mt-0.5"></i>
                <div>
                    <p class="text-ink font-medium">This action cannot be undone</p>
                    <p class="text-stone text-sm">The thought will be permanently removed from the database.</p>
                </div>
            </div>
        </div>

        <div class="text-center mb-12">
            <p class="text-sm font-medium text-coral uppercase tracking-widest mb-4 inline-flex items-center gap-2">
                <i data-lucide="trash-2" class="w-4 h-4"></i>
                Delete Confirmation
            </p>
            <h1 class="font-display text-display-md text-ink inline-flex items-center gap-3">
                <i data-lucide="alert-triangle" class="w-6 h-6 text-coral"></i>
                Are you sure?
            </h1>
        </div>

        <div class="border border-mist p-8 mb-8 bg-wheat/5">
            <p class="font-display text-2xl text-ink mb-2"><?= h($thought['title']) ?></p>
            <p class="text-stone mb-4">By <?= h($thought['author']) ?> • <?= h($thought['category']) ?></p>
            <p class="text-stone leading-relaxed"><?= h($thought['thought']) ?></p>
            <div class="mt-4 pt-4 border-t border-mist">
                <span class="text-wheat"><?= str_repeat('★', (int) $thought['mood_score']) ?></span>
            </div>
        </div>

        <form method="post" action="<?= h(route_url('delete', ['id' => $id])) ?>" class="flex flex-wrap justify-center gap-4">
            <input type="hidden" name="csrf_token" value="<?= h(csrf_token()) ?>">
            <a href="<?= h(route_url('thoughts')) ?>" class="btn-secondary">
                <i data-lucide="badge-x" class="w-4 h-4"></i>
                Keep this thought
            </a>
            <button type="submit" class="btn-destructive">
                <i data-lucide="trash-2" class="w-4 h-4"></i>
                Delete permanently
            </button>
        </form>
    </div>
</section>

<?php require project_root_path() . '/app/views/layout/footer.php'; ?>
