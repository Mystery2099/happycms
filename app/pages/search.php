<?php

declare(strict_types=1);

require_once dirname(__DIR__, 2) . '/app/bootstrap.php';
require_request_method(['GET']);

$query = normalized_search_query($_GET['q'] ?? '');
$thoughts = all_thoughts($query);
$pageTitle = 'Search Thoughts';
$pageDescription = 'Search the happy thoughts database.';
$currentPage = 'search';
$libraryHeading = $query === '' ? 'Search' : 'Results for "' . $query . '"';
$libraryDescription = $query === '' ? 'Enter a search term to find thoughts by title, author, category, or content.' : 'Found ' . count($thoughts) . ' matching thought' . (count($thoughts) === 1 ? '' : 's') . '.';
$emptyMessage = $query === '' ? 'Enter a search term above to get started.' : 'No thoughts found matching "' . $query . '".';
$serverSearch = $query;

require project_root_path() . '/app/views/layout/header.php';
?>

<section class="section-padding">
    <div class="max-w-6xl mx-auto px-6 lg:px-8">
        <!-- Search Header -->
        <div class="max-w-2xl mb-12">
            <p class="text-sm font-medium text-stone uppercase tracking-widest mb-4">Search</p>
            <h1 class="font-display text-display-md text-ink mb-6 inline-flex items-center gap-3">
                <i data-lucide="search" class="w-6 h-6 text-coral"></i>
                Find happy thoughts
            </h1>
            
            <form action="<?= h(route_url('search')) ?>" method="get" class="flex gap-3" role="search">
                <label for="search-query" class="sr-only">Search thoughts</label>
                <input
                    id="search-query"
                    type="search"
                    name="q"
                    value="<?= h($query) ?>"
                    placeholder="Search by title, author, category, or content..."
                    class="input-minimal flex-1"
                    aria-label="Search thoughts"
                >
                <button type="submit" class="btn-primary">
                    <i data-lucide="search" class="w-4 h-4"></i>
                    Search
                </button>
            </form>
        </div>

        <!-- Results -->
        <?php require project_root_path() . '/app/views/partials/thought-library.php'; ?>
    </div>
</section>

<?php require project_root_path() . '/app/views/layout/footer.php'; ?>
