<?php

declare(strict_types=1);

$thoughtData = $thoughtData ?? [
    'title' => '',
    'author' => '',
    'category' => 'Nature',
    'mood_score' => 3,
    'thought' => '',
    'image_path' => '',
];
$errors = $errors ?? [];
$submitLabel = $submitLabel ?? 'Save Thought';
$formAction = $formAction ?? '';
?>
<form method="post" action="<?= h($formAction) ?>" class="max-w-2xl">
    <input type="hidden" name="csrf_token" value="<?= h(csrf_token()) ?>">
    <div class="space-y-8">
        <?php if (isset($errors['form'])): ?>
            <div class="border border-coral/20 bg-coral/10 px-4 py-3 text-sm text-coral" role="alert">
                <?= h($errors['form']) ?>
            </div>
        <?php endif; ?>

        <!-- Title & Author -->
        <div class="grid md:grid-cols-2 gap-8">
            <div>
                <label for="thought-title" class="block text-sm font-medium text-stone mb-2 inline-flex items-center gap-2">
                    <i data-lucide="type" class="w-4 h-4"></i>
                    Title
                </label>
                <input
                    id="thought-title"
                    type="text"
                    name="title"
                    value="<?= h($thoughtData['title']) ?>"
                    class="input-minimal"
                    placeholder="Give your thought a title"
                    required
                    minlength="3"
                    maxlength="80"
                >
                <?php if (isset($errors['title'])): ?>
                    <p class="mt-2 text-sm text-coral"><?= h($errors['title']) ?></p>
                <?php endif; ?>
            </div>

            <div>
                <label for="thought-author" class="block text-sm font-medium text-stone mb-2 inline-flex items-center gap-2">
                    <i data-lucide="user" class="w-4 h-4"></i>
                    Author
                </label>
                <input
                    id="thought-author"
                    type="text"
                    name="author"
                    value="<?= h($thoughtData['author']) ?>"
                    class="input-minimal"
                    placeholder="Your name"
                    required
                    minlength="2"
                    maxlength="60"
                >
                <?php if (isset($errors['author'])): ?>
                    <p class="mt-2 text-sm text-coral"><?= h($errors['author']) ?></p>
                <?php endif; ?>
            </div>
        </div>

        <!-- Category & Mood -->
        <div class="grid md:grid-cols-2 gap-8">
            <div>
                <label for="thought-category" class="block text-sm font-medium text-stone mb-2 inline-flex items-center gap-2">
                    <i data-lucide="tag" class="w-4 h-4"></i>
                    Category
                </label>
                <select id="thought-category" name="category" class="input-minimal bg-transparent">
                    <?php foreach (THOUGHT_CATEGORIES as $category): ?>
                        <option value="<?= h($category) ?>" <?= $thoughtData['category'] === $category ? 'selected' : '' ?>>
                            <?= h($category) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <?php if (isset($errors['category'])): ?>
                    <p class="mt-2 text-sm text-coral"><?= h($errors['category']) ?></p>
                <?php endif; ?>
            </div>

            <div>
                <label for="thought-mood" class="block text-sm font-medium text-stone mb-2 inline-flex items-center gap-2">
                    <i data-lucide="smile-plus" class="w-4 h-4"></i>
                    Mood Score (1-5)
                </label>
                <input
                    id="thought-mood"
                    type="number"
                    name="mood_score"
                    value="<?= h((string) $thoughtData['mood_score']) ?>"
                    min="1"
                    max="5"
                    class="input-minimal"
                >
                <?php if (isset($errors['mood_score'])): ?>
                    <p class="mt-2 text-sm text-coral"><?= h($errors['mood_score']) ?></p>
                <?php endif; ?>
            </div>
        </div>

        <!-- Thought Text -->
        <div>
            <label for="thought-content" class="block text-sm font-medium text-stone mb-2 inline-flex items-center gap-2">
                <i data-lucide="message-square-text" class="w-4 h-4"></i>
                Your Happy Thought
            </label>
            <textarea
                id="thought-content"
                name="thought"
                rows="5"
                class="input-minimal resize-none"
                placeholder="Share what made you happy..."
                required
                minlength="12"
                maxlength="400"
            ><?= h($thoughtData['thought']) ?></textarea>
            <p class="mt-2 text-xs text-stone">Minimum 12 characters, maximum 400</p>
            <?php if (isset($errors['thought'])): ?>
                <p class="mt-2 text-sm text-coral"><?= h($errors['thought']) ?></p>
            <?php endif; ?>
        </div>

        <!-- Image Path -->
        <div>
            <label for="thought-image" class="block text-sm font-medium text-stone mb-2 inline-flex items-center gap-2">
                <i data-lucide="image" class="w-4 h-4"></i>
                Image Path (Optional)
            </label>
            <input
                id="thought-image"
                type="text"
                name="image_path"
                value="<?= h((string) $thoughtData['image_path']) ?>"
                class="input-minimal"
                placeholder="images/spring-hero.jpg"
            >
            <p class="mt-2 text-xs text-stone">Optional. Use a local image path from this project.</p>
            <?php if (isset($errors['image_path'])): ?>
                <p class="mt-2 text-sm text-coral"><?= h($errors['image_path']) ?></p>
            <?php endif; ?>
        </div>

        <!-- Actions -->
        <div class="flex flex-wrap gap-4 pt-4">
            <button type="submit" class="btn-primary">
                <i data-lucide="save" class="w-4 h-4"></i>
                <?= h($submitLabel) ?>
            </button>
            <a href="<?= h(route_url('thoughts')) ?>" class="btn-secondary">
                <i data-lucide="badge-x" class="w-4 h-4"></i>
                Cancel
            </a>
        </div>
    </div>
</form>
