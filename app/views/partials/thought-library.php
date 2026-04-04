<?php

declare(strict_types=1);

$thoughts = $thoughts ?? [];
$emptyMessage = $emptyMessage ?? 'No happy thoughts yet. Be the first to add one!';
$libraryHeading = $libraryHeading ?? 'All Thoughts';
$libraryDescription = $libraryDescription ?? 'Browse and manage your collection of happy moments.';
$serverSearch = $serverSearch ?? '';
?>
<section class="space-y-6 md:space-y-8">
    <!-- Header & Search -->
    <div class="flex flex-col sm:flex-row sm:items-end sm:justify-between gap-4">
        <div>
            <h2 class="font-display text-display-md text-ink mb-1 md:mb-2"><?= h($libraryHeading) ?></h2>
            <p class="text-stone text-sm md:text-base hidden sm:block"><?= h($libraryDescription) ?></p>
        </div>

        <?php if (($currentPage ?? '') !== 'search'): ?>
            <a href="<?= h(route_url('search')) ?>" class="btn-secondary text-sm md:text-base py-2 md:py-3">
                <i data-lucide="search" class="w-4 h-4"></i>
                Search thoughts
            </a>
        <?php endif; ?>
    </div>

    <!-- Live Controls (Svelte Component) -->
    <div
        id="thought-controls"
        data-thought-controls
        data-categories="<?= h(json_encode(THOUGHT_CATEGORIES, JSON_THROW_ON_ERROR)) ?>"
    ></div>

    <?php if (!$thoughts): ?>
        <!-- Empty State -->
        <div class="text-center py-16 border border-dashed border-mist">
            <p class="font-display text-xl text-ink mb-2"><?= h($emptyMessage) ?></p>
            <p class="text-stone mb-6">Start collecting happy moments</p>
            <a href="<?= h(route_url('create')) ?>" class="btn-primary">
                <i data-lucide="plus-circle" class="w-4 h-4"></i>
                Add Your First Thought
            </a>
        </div>
    <?php else: ?>
        <!-- Table View -->
        <div data-thought-library data-default-view="table" class="border border-mist overflow-hidden">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Thought</th>
                        <th>Author</th>
                        <th>Category</th>
                        <th>Mood</th>
                        <th class="text-right">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($thoughts as $thought): ?>
                        <tr
                            data-thought-item
                            data-record-id="<?= (int) $thought['id'] ?>"
                            data-title="<?= h(strtolower($thought['title'])) ?>"
                            data-author="<?= h(strtolower($thought['author'])) ?>"
                            data-category="<?= h(strtolower($thought['category'])) ?>"
                            data-thought="<?= h(strtolower($thought['thought'])) ?>"
                        >
                            <td class="max-w-md">
                                <p class="font-medium text-ink"><?= h($thought['title']) ?></p>
                                <p class="mt-1 text-sm text-stone whitespace-normal break-words"><?= h($thought['thought']) ?></p>
                                <?php if (!empty($thought['image_path'])): ?>
                                    <span class="inline-flex items-center gap-1 mt-2 text-xs text-stone">
                                        <i data-lucide="image" class="w-3 h-3"></i>
                                        Has image
                                    </span>
                                <?php endif; ?>
                            </td>
                            <td class="text-stone"><?= h($thought['author']) ?></td>
                            <td>
                                <span class="inline-flex items-center px-2.5 py-0.5 text-xs font-medium bg-mist/50 text-stone">
                                    <?= h($thought['category']) ?>
                                </span>
                            </td>
                            <td class="text-wheat"><?= str_repeat('★', (int) $thought['mood_score']) ?></td>
                            <td class="text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <a href="<?= h(route_url('edit', ['id' => (int) $thought['id']])) ?>" class="text-sm text-stone hover:text-ink transition-colors inline-flex items-center gap-1.5">
                                        <i data-lucide="pencil" class="w-3.5 h-3.5"></i>
                                        Edit
                                    </a>
                                    <span class="text-mist">|</span>
                                    <a href="<?= h(route_url('delete', ['id' => (int) $thought['id']])) ?>" class="text-sm text-coral hover:text-ink transition-colors inline-flex items-center gap-1.5">
                                        <i data-lucide="trash-2" class="w-3.5 h-3.5"></i>
                                        Delete
                                    </a>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <!-- Card Grid (Visible by default on mobile) -->
        <div data-card-grid class="hidden grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 md:gap-6">
            <?php foreach ($thoughts as $thought): ?>
                <article
                    data-thought-item
                    data-record-id="<?= (int) $thought['id'] ?>"
                    data-title="<?= h(strtolower($thought['title'])) ?>"
                    data-author="<?= h(strtolower($thought['author'])) ?>"
                    data-category="<?= h(strtolower($thought['category'])) ?>"
                    data-thought="<?= h(strtolower($thought['thought'])) ?>"
                    class="group bg-white rounded-xl border border-mist overflow-hidden transition-all duration-200 hover:shadow-md"
                >
                    <?php if (!empty($thought['image_path'])): ?>
                        <div class="aspect-[16/10] overflow-hidden">
                            <img src="<?= h(asset_url((string) $thought['image_path'])) ?>" alt="<?= h($thought['title']) ?>" class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-105">
                        </div>
                    <?php endif; ?>
                    <div class="p-4 md:p-5 space-y-3">
                        <div class="flex items-start justify-between gap-3">
                            <h3 class="font-display text-lg md:text-xl text-ink leading-tight"><?= h($thought['title']) ?></h3>
                            <span class="inline-flex items-center px-2.5 py-1 text-xs font-medium bg-coral/10 text-coral rounded-full shrink-0">
                                <?= h($thought['category']) ?>
                            </span>
                        </div>
                        <p class="text-sm text-stone leading-relaxed whitespace-normal break-words"><?= h($thought['thought']) ?></p>
                        <div class="flex items-center justify-between pt-3 border-t border-mist/60">
                            <span class="text-sm font-medium text-ink"><?= h($thought['author']) ?></span>
                            <span class="text-wheat text-sm"><?= str_repeat('★', (int) $thought['mood_score']) ?></span>
                        </div>
                        <div class="flex items-center gap-4 pt-2">
                            <a href="<?= h(route_url('edit', ['id' => (int) $thought['id']])) ?>" class="flex items-center gap-1.5 text-sm font-medium text-stone hover:text-ink transition-colors">
                                <i data-lucide="pencil" class="w-3.5 h-3.5"></i>
                                Edit
                            </a>
                            <a href="<?= h(route_url('delete', ['id' => (int) $thought['id']])) ?>" class="flex items-center gap-1.5 text-sm font-medium text-coral hover:text-ink transition-colors">
                                <i data-lucide="trash-2" class="w-3.5 h-3.5"></i>
                                Delete
                            </a>
                        </div>
                    </div>
                </article>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</section>
