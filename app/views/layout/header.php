<?php

declare(strict_types=1);

$pageTitle = $pageTitle ?? 'Happy Thoughts';
$pageDescription = $pageDescription ?? 'A collection of happy moments and positive reflections.';
$currentPage = $currentPage ?? '';
$flash = get_flash();
$cspNonce = csp_nonce();
send_page_security_headers();
$headerProps = shell_component_props($currentPage);
$footerProps = shell_component_props($currentPage);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csp-nonce" content="<?= h($cspNonce) ?>">
    <title><?= h($pageTitle) ?></title>
    <meta name="description" content="<?= h($pageDescription) ?>">
    <link rel="icon" href="<?= h(asset_url('public/icons/favicon.svg')) ?>" type="image/svg+xml">
    <link rel="icon" href="<?= h(asset_url('favicon.ico')) ?>" sizes="any">
    <link rel="apple-touch-icon" href="<?= h(asset_url('public/icons/apple-touch-icon.png')) ?>">
    <script nonce="<?= h($cspNonce) ?>">
        (function() {
            const THEME_STORAGE_KEY = 'happy-thoughts-theme';
            const html = document.documentElement;
            const storedTheme = localStorage.getItem(THEME_STORAGE_KEY) || 'system';
            const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
            const resolvedTheme = storedTheme === 'system'
                ? (prefersDark ? 'dark' : 'light')
                : storedTheme;

            html.classList.toggle('dark', resolvedTheme === 'dark');
            html.style.colorScheme = resolvedTheme;
        })();
    </script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Sora:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= h(asset_url('public/assets/app.css')) ?>">
</head>
<body class="min-h-screen flex flex-col">
    <a class="skip-link" href="#main-content">Skip to main content</a>

    <div data-site-header></div>
    <script id="site-header-props" type="application/json"><?= page_props_json($headerProps) ?></script>

    <?php if ($flash): ?>
        <div data-flash-banner></div>
        <script id="flash-banner-props" type="application/json"><?= page_props_json(flash_component_props($flash)) ?></script>
    <?php endif; ?>

    <main id="main-content" class="flex-1 pb-20 md:pb-0" tabindex="-1">

    <script nonce="<?= h($cspNonce) ?>">
        // Seamless scroll detection - only adds visual class, no layout changes
        (function() {
            const header = document.querySelector('[data-header]');
            if (!header) return;

            // Use requestAnimationFrame for smooth 60fps scroll handling
            let ticking = false;
            let lastScrollY = 0;
            const scrollThreshold = 10; // pixels to trigger visual change

            function updateHeader() {
                const scrolled = window.scrollY > scrollThreshold;
                header.classList.toggle('is-scrolled', scrolled);
                ticking = false;
            }

            function onScroll() {
                lastScrollY = window.scrollY;
                if (!ticking) {
                    requestAnimationFrame(updateHeader);
                    ticking = true;
                }
            }

            // Passive listener for better scroll performance
            window.addEventListener('scroll', onScroll, { passive: true });
            
            // Check initial state
            updateHeader();

            // Theme management
            const THEME_STORAGE_KEY = 'happy-thoughts-theme';
            
            // Get saved theme or default to system
            let currentTheme = localStorage.getItem(THEME_STORAGE_KEY) || 'system';
            
            // Apply theme to document
            function setDocumentTheme(theme) {
                const html = document.documentElement;
                
                if (theme === 'system') {
                    const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
                    html.classList.toggle('dark', prefersDark);
                    html.style.colorScheme = prefersDark ? 'dark' : 'light';
                } else if (theme === 'dark') {
                    html.classList.add('dark');
                    html.style.colorScheme = 'dark';
                } else {
                    html.classList.remove('dark');
                    html.style.colorScheme = 'light';
                }
            }

            function syncThemeState(theme) {
                currentTheme = theme;
                localStorage.setItem(THEME_STORAGE_KEY, theme);
                updateThemeUI();
            }

            function broadcastTheme(theme) {
                window.dispatchEvent(new CustomEvent('happy-theme-change', {
                    detail: { theme }
                }));
            }

            function applyTheme(theme) {
                setDocumentTheme(theme);
                syncThemeState(theme);
                broadcastTheme(theme);
            }
            
            // Update UI elements
            function updateThemeUI() {
                // Theme triggers are Svelte components and sync through the shared event.
            }

            window.addEventListener('happy-theme-change', (event) => {
                const nextTheme = event.detail?.theme;
                if (nextTheme === 'system' || nextTheme === 'light' || nextTheme === 'dark') {
                    setDocumentTheme(nextTheme);
                    syncThemeState(nextTheme);
                }
            });
            
            // Listen for system theme changes
            const mediaQuery = window.matchMedia('(prefers-color-scheme: dark)');
            mediaQuery.addEventListener('change', () => {
                if (currentTheme === 'system') {
                    applyTheme('system');
                }
            });
            
            // Apply initial theme
            applyTheme(currentTheme);
        })();
    </script>
