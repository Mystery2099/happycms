<?php

declare(strict_types=1);

require_request_method(['GET']);

$stats = dashboard_stats();
$recentThoughts = recent_thoughts(4);
$pageTitle = 'Happy Thoughts | Home';
$pageDescription = 'A collection of happy moments and positive reflections.';
$currentPage = 'home';

require project_root_path() . '/app/views/layout/header.php';
?>

<!-- Hero Section -->
<section class="border-b border-mist">
    <div class="max-w-6xl mx-auto px-6 lg:px-8 py-16 lg:py-24">
        <div class="grid lg:grid-cols-2 gap-12 lg:gap-16 items-center">
            <div>
                <p class="text-sm font-medium text-stone uppercase tracking-widest mb-4">CISY 7203</p>
                <h1 class="font-display text-display-lg text-ink mb-6">
                    A collection of happy thoughts for brighter days.
                </h1>
                <p class="text-stone leading-relaxed mb-8 max-w-lg">
                    This content management system demonstrates PHP, SQLite, Svelte, and modern web development.
                    Create, browse, and search through moments of joy.
                </p>
                <div class="flex flex-wrap gap-4">
                    <a href="<?= h(route_url('create')) ?>" class="btn-primary">
                        <i data-lucide="plus-circle" class="w-4 h-4"></i>
                        Add a Happy Thought
                    </a>
                    <a href="<?= h(route_url('thoughts')) ?>" class="btn-secondary">
                        <i data-lucide="file-text" class="w-4 h-4"></i>
                        Browse Collection
                    </a>
                </div>
            </div>
            <div class="relative">
                <img src="<?= h(asset_url('images/spring-hero.jpg')) ?>" alt="Spring flowers in warm light" class="w-full aspect-[4/3] object-cover">
                <div class="absolute -bottom-4 -left-4 bg-white p-4 shadow-lg hidden lg:block">
                    <i data-lucide="smile-plus" class="w-5 h-5 text-coral mb-2"></i>
                    <p class="font-display text-2xl text-ink"><?= $stats['total'] ?></p>
                    <p class="text-sm text-stone">Happy thoughts collected</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Stats Bar -->
<section class="border-b border-mist bg-white">
    <div class="max-w-6xl mx-auto px-6 lg:px-8">
        <div class="grid grid-cols-2 md:grid-cols-4 divide-x divide-mist">
            <div class="py-8 px-4 text-center">
                <i data-lucide="file-text" class="w-5 h-5 text-coral mx-auto mb-2"></i>
                <p class="font-display text-3xl text-ink"><?= $stats['total'] ?></p>
                <p class="text-sm text-stone mt-1">Total Records</p>
            </div>
            <div class="py-8 px-4 text-center">
                <i data-lucide="tag" class="w-5 h-5 text-coral mx-auto mb-2"></i>
                <p class="font-display text-3xl text-ink"><?= $stats['categories'] ?></p>
                <p class="text-sm text-stone mt-1">Categories</p>
            </div>
            <div class="py-8 px-4 text-center">
                <i data-lucide="star" class="w-5 h-5 text-coral mx-auto mb-2"></i>
                <p class="font-display text-3xl text-ink"><?= $stats['high_mood'] ?></p>
                <p class="text-sm text-stone mt-1">High Mood (4-5★)</p>
            </div>
            <div class="py-8 px-4 text-center">
                <i data-lucide="image" class="w-5 h-5 text-coral mx-auto mb-2"></i>
                <p class="font-display text-3xl text-ink"><?= $stats['with_images'] ?></p>
                <p class="text-sm text-stone mt-1">With Images</p>
            </div>
        </div>
    </div>
</section>

<!-- Recent Thoughts Table -->
<section class="section-padding">
    <div class="max-w-6xl mx-auto px-6 lg:px-8">
        <div class="flex items-end justify-between mb-8">
            <div>
                <h2 class="font-display text-display-md text-ink mb-2">Recent Entries</h2>
                <p class="text-stone">The latest happy thoughts from our collection</p>
            </div>
            <a href="<?= h(route_url('thoughts')) ?>" class="editorial-link text-sm inline-flex items-center gap-2">
                View all thoughts
                <i data-lucide="arrow-right" class="w-4 h-4"></i>
            </a>
        </div>

        <div class="overflow-hidden border border-mist">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Title</th>
                        <th>Author</th>
                        <th>Category</th>
                        <th class="text-right">Mood</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($recentThoughts as $thought): ?>
                        <tr>
                            <td>
                                <a href="<?= h(route_url('edit', ['id' => (int) $thought['id']])) ?>" class="font-medium text-ink hover:text-coral transition-colors">
                                    <?= h($thought['title']) ?>
                                </a>
                                <p class="text-sm text-stone line-clamp-1 mt-1"><?= h($thought['thought']) ?></p>
                            </td>
                            <td class="text-stone"><?= h($thought['author']) ?></td>
                            <td>
                                <span class="inline-flex items-center px-2.5 py-0.5 text-xs font-medium bg-mist/50 text-stone">
                                    <?= h($thought['category']) ?>
                                </span>
                            </td>
                            <td class="text-right text-wheat">
                                <?= str_repeat('★', (int) $thought['mood_score']) ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</section>

<!-- Features & Structure -->
<section class="section-padding border-t border-mist bg-white">
    <div class="max-w-6xl mx-auto px-6 lg:px-8">
        <div class="grid lg:grid-cols-2 gap-16">
            <div>
                <h2 class="font-display text-display-md text-ink mb-6">What This Site Demonstrates</h2>
                <div class="space-y-4">
                    <div class="flex gap-4">
                        <span class="text-coral font-medium inline-flex items-center gap-2"><i data-lucide="file-text" class="w-4 h-4"></i>01</span>
                        <div>
                            <p class="font-medium text-ink">Template System</p>
                            <p class="text-sm text-stone">Shared header and footer includes for consistent layout</p>
                        </div>
                    </div>
                    <div class="flex gap-4">
                        <span class="text-coral font-medium inline-flex items-center gap-2"><i data-lucide="type" class="w-4 h-4"></i>02</span>
                        <div>
                            <p class="font-medium text-ink">HTML Structure</p>
                            <p class="text-sm text-stone">Tables, headings, lists, images, and semantic elements</p>
                        </div>
                    </div>
                    <div class="flex gap-4">
                        <span class="text-coral font-medium inline-flex items-center gap-2"><i data-lucide="save" class="w-4 h-4"></i>03</span>
                        <div>
                            <p class="font-medium text-ink">Server-Side Processing</p>
                            <p class="text-sm text-stone">Form handling, validation, and database operations</p>
                        </div>
                    </div>
                    <div class="flex gap-4">
                        <span class="text-coral font-medium inline-flex items-center gap-2"><i data-lucide="search" class="w-4 h-4"></i>04</span>
                        <div>
                            <p class="font-medium text-ink">AJAX Integration</p>
                            <p class="text-sm text-stone">Fetch API for loading famous quotes dynamically</p>
                        </div>
                    </div>
                </div>
            </div>

            <div>
                <h2 class="font-display text-display-md text-ink mb-6">Quick Links</h2>
                <nav class="space-y-3">
                    <a href="<?= h(route_url('create')) ?>" class="flex items-center justify-between py-3 border-b border-mist group">
                        <span class="text-ink group-hover:text-coral transition-colors inline-flex items-center gap-3">
                            <i data-lucide="plus-circle" class="w-4 h-4"></i>
                            Create a new record
                        </span>
                        <i data-lucide="arrow-right" class="w-4 h-4 text-stone"></i>
                    </a>
                    <a href="<?= h(route_url('search')) ?>" class="flex items-center justify-between py-3 border-b border-mist group">
                        <span class="text-ink group-hover:text-coral transition-colors inline-flex items-center gap-3">
                            <i data-lucide="search" class="w-4 h-4"></i>
                            Search the database
                        </span>
                        <i data-lucide="arrow-right" class="w-4 h-4 text-stone"></i>
                    </a>
                    <a href="<?= h(route_url('thoughts')) ?>" class="flex items-center justify-between py-3 border-b border-mist group">
                        <span class="text-ink group-hover:text-coral transition-colors inline-flex items-center gap-3">
                            <i data-lucide="file-text" class="w-4 h-4"></i>
                            Manage all records
                        </span>
                        <i data-lucide="arrow-right" class="w-4 h-4 text-stone"></i>
                    </a>
                </nav>

                <div class="mt-8 pt-8 border-t border-mist">
                    <h3 class="font-display text-lg text-ink mb-3 inline-flex items-center gap-2">
                        <i data-lucide="map-pin" class="w-4 h-4 text-coral"></i>
                        Contact
                    </h3>
                    <address class="not-italic text-stone text-sm leading-relaxed">
                        Happy Thoughts Studio<br>
                        123 Campus Garden Walk<br>
                        Boston, MA 02115
                    </address>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Famous Thoughts Section -->
<section class="section-padding border-t border-mist">
    <div class="max-w-6xl mx-auto px-6 lg:px-8">
        <div id="happy-dashboard" data-happy-dashboard data-api-url="<?= h(asset_url('api/famous-thoughts.php')) ?>"></div>
    </div>
</section>

<?php require project_root_path() . '/app/views/layout/footer.php'; ?>
