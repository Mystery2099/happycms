    </main>

    <!-- Bottom Navigation - Mobile Only -->
    <nav class="bottom-nav md:hidden" aria-label="Mobile navigation">
        <div class="bottom-nav-items">
            <a href="<?= h(route_url('home')) ?>" class="bottom-nav-item <?= $currentPage === 'home' ? 'is-active' : '' ?>">
                <i data-lucide="home" class="w-5 h-5"></i>
                <span class="bottom-nav-label">Home</span>
            </a>
            <a href="<?= h(route_url('thoughts')) ?>" class="bottom-nav-item <?= $currentPage === 'thoughts' ? 'is-active' : '' ?>">
                <i data-lucide="file-text" class="w-5 h-5"></i>
                <span class="bottom-nav-label">Thoughts</span>
            </a>
            <a href="<?= h(route_url('create')) ?>" class="bottom-nav-item <?= $currentPage === 'create' ? 'is-active' : '' ?>">
                <i data-lucide="plus-circle" class="w-5 h-5"></i>
                <span class="bottom-nav-label">Add</span>
            </a>
            <a href="<?= h(route_url('search')) ?>" class="bottom-nav-item <?= $currentPage === 'search' ? 'is-active' : '' ?>">
                <i data-lucide="search" class="w-5 h-5"></i>
                <span class="bottom-nav-label">Search</span>
            </a>
        </div>
    </nav>

    <footer class="border-t border-mist mt-auto hidden md:block">
        <div class="max-w-6xl mx-auto px-6 lg:px-8 py-12">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-6">
                <div>
                    <p class="font-display text-lg text-ink">Happy Thoughts</p>
                    <p class="text-sm text-stone mt-1">Collecting moments of joy since 2026</p>
                </div>
                <div class="flex items-center gap-6 text-sm text-stone">
                    <span>PHP + SQLite</span>
                    <span class="text-mist">|</span>
                    <span>Svelte + TypeScript</span>
                    <span class="text-mist">|</span>
                    <span>Tailwind CSS</span>
                </div>
            </div>
        </div>
    </footer>

    <script type="module" src="<?= h(asset_url('public/assets/app.js')) ?>"></script>
</body>
</html>
