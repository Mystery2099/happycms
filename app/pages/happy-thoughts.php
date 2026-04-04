<?php

declare(strict_types=1);

require_once dirname(__DIR__, 2) . '/app/bootstrap.php';
require_request_method(['GET']);

$thoughts = all_thoughts();
$pageTitle = 'All Thoughts';
$pageDescription = 'Browse and manage your collection of happy moments.';
$currentPage = 'thoughts';
$libraryHeading = 'Thought Collection';
$libraryDescription = 'Browse, filter, and manage all your happy thoughts.';
$emptyMessage = 'No thoughts yet. Add your first happy moment!';
$serverSearch = '';

require project_root_path() . '/app/views/layout/header.php';
?>

<section class="section-padding">
    <div class="max-w-6xl mx-auto px-6 lg:px-8">
        <?php require project_root_path() . '/app/views/partials/thought-library.php'; ?>
    </div>
</section>

<?php require project_root_path() . '/app/views/layout/footer.php'; ?>
