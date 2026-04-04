<?php

declare(strict_types=1);

$pageTitle = $pageTitle ?? 'Happy Thoughts';
$pageDescription = $pageDescription ?? 'A collection of happy moments and positive reflections.';
$currentPage = $currentPage ?? '';
$flash = get_flash();
$cspNonce = csp_nonce();
send_page_security_headers();
$navItems = [
    'home' => 'Home',
    'thoughts' => 'Thoughts',
    'create' => 'Add Thought',
    'search' => 'Search',
];
$navIcons = [
    'home' => 'home',
    'thoughts' => 'file-text',
    'create' => 'plus-circle',
    'search' => 'search',
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= h($pageTitle) ?></title>
    <meta name="description" content="<?= h($pageDescription) ?>">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Sora:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= h(asset_url('public/assets/app.css')) ?>">
    <style nonce="<?= h($cspNonce) ?>">
        /* Seamless Sticky Header - Always sticky, visual changes only */
        .site-header {
            position: sticky;
            top: 0;
            z-index: 50;
            /* Start with base state */
            background: rgba(250, 249, 247, 0);
            border-bottom: 1px solid rgba(231, 229, 228, 1);
            transition: 
                background-color 0.25s ease-out,
                border-color 0.25s ease-out,
                box-shadow 0.25s ease-out;
        }

        /* When scrolled - add visual depth */
        .site-header.is-scrolled {
            background: rgba(250, 249, 247, 0.95);
            backdrop-filter: blur(8px);
            -webkit-backdrop-filter: blur(8px);
            border-bottom-color: rgba(231, 229, 228, 0.6);
            box-shadow: 0 1px 0 rgba(0, 0, 0, 0.03);
        }

        /* Dark theme support - midnight navy */
        html.dark .site-header {
            background: rgba(15, 23, 42, 0);
            border-bottom-color: rgba(51, 65, 85, 0.5);
        }

        html.dark .site-header.is-scrolled {
            background: rgba(15, 23, 42, 0.95);
            border-bottom-color: rgba(51, 65, 85, 0.8);
        }

        /* Header inner - consistent height always */
        .site-header .header-inner {
            height: 80px;
            transition: height 0.2s ease-out;
        }

        /* Reduced motion */
        @media (prefers-reduced-motion: reduce) {
            .site-header {
                transition: none;
            }
            .site-header .header-inner {
                transition: none;
            }
        }

        /* Theme selector dropdown */
        .theme-selector {
            position: relative;
        }

        .theme-selector-btn {
            display: flex;
            align-items: center;
            gap: 6px;
            padding: 6px 10px;
            border-radius: 6px;
            font-size: 13px;
            font-weight: 500;
            color: var(--stone, #57534e);
            background: transparent;
            border: 1px solid transparent;
            transition: all 0.15s ease;
            cursor: pointer;
        }

        .theme-selector-btn:hover {
            background: rgba(0, 0, 0, 0.04);
            border-color: rgba(0, 0, 0, 0.08);
        }

        html.dark .theme-selector-btn {
            color: #94a3b8;
        }

        html.dark .theme-selector-btn:hover {
            background: rgba(255, 255, 255, 0.06);
            border-color: rgba(255, 255, 255, 0.1);
        }

        .theme-dropdown {
            position: absolute;
            top: 100%;
            right: 0;
            margin-top: 8px;
            min-width: 140px;
            background: rgba(250, 249, 247, 0.98);
            border: 1px solid rgba(231, 229, 228, 0.8);
            border-radius: 8px;
            padding: 4px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
            opacity: 0;
            visibility: hidden;
            transform: translateY(-8px);
            transition: all 0.15s ease;
            z-index: 100;
        }

        .theme-dropdown.is-open,
        .theme-dropdown--mounted {
            opacity: 1;
            visibility: visible;
            transform: translateY(0);
        }

        html.dark .theme-dropdown {
            background: rgba(15, 23, 42, 0.98);
            border-color: rgba(51, 65, 85, 0.6);
        }

        .theme-option {
            display: flex;
            align-items: center;
            gap: 8px;
            width: 100%;
            padding: 8px 10px;
            border-radius: 6px;
            font-size: 13px;
            color: var(--ink, #1a1a1a);
            background: transparent;
            border: none;
            cursor: pointer;
            transition: background 0.1s ease;
        }

        .theme-option:hover {
            background: rgba(0, 0, 0, 0.04);
        }

        .theme-option.is-active {
            background: rgba(220, 95, 80, 0.1);
            color: #dc5f50;
        }

        html.dark .theme-option {
            color: #f8fafc;
        }

        html.dark .theme-option:hover {
            background: rgba(255, 255, 255, 0.06);
        }

        html.dark .theme-option.is-active {
            background: rgba(251, 113, 133, 0.15);
            color: #fb7185;
        }

        /* Mobile theme toggle button */
        .mobile-theme-toggle {
            display: flex;
            align-items: center;
            justify-content: flex-start;
            gap: 8px;
            padding: 12px 8px;
            font-size: 16px;
            font-weight: 500;
            color: var(--stone, #57534e);
            background: transparent;
            border: none;
            border-top: 1px solid rgba(231, 229, 228, 0.5);
            margin-top: 8px;
            cursor: pointer;
            transition: color 0.2s ease, background-color 0.2s ease;
            border-radius: 6px;
        }

        .mobile-theme-toggle:hover {
            background-color: rgba(0, 0, 0, 0.04);
        }

        html.dark .mobile-theme-toggle {
            color: #94a3b8;
            border-color: rgba(51, 65, 85, 0.5);
        }

        html.dark .mobile-theme-toggle:hover {
            background-color: rgba(255, 255, 255, 0.06);
        }

        /* Bottom Navigation Bar - Mobile Only */
        .bottom-nav {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            z-index: 100;
            background: rgba(250, 249, 247, 0.98);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border-top: 1px solid rgba(231, 229, 228, 0.8);
            padding-bottom: env(safe-area-inset-bottom, 0px);
        }

        html.dark .bottom-nav {
            background: rgba(15, 23, 42, 0.98);
            border-top-color: rgba(51, 65, 85, 0.6);
        }

        .bottom-nav-items {
            display: flex;
            justify-content: space-around;
            align-items: center;
            height: 64px;
        }

        .bottom-nav-item {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            gap: 4px;
            flex: 1;
            height: 100%;
            padding: 8px 4px;
            color: var(--stone, #57534e);
            text-decoration: none;
            transition: color 0.15s ease;
            position: relative;
        }

        .bottom-nav-item:hover,
        .bottom-nav-item:active {
            color: var(--ink, #1a1a1a);
        }

        html.dark .bottom-nav-item {
            color: #94a3b8;
        }

        html.dark .bottom-nav-item:hover,
        html.dark .bottom-nav-item:active {
            color: #f8fafc;
        }

        .bottom-nav-item.is-active {
            color: #dc5f50;
        }

        html.dark .bottom-nav-item.is-active {
            color: #fb7185;
        }

        .bottom-nav-item i {
            opacity: 0.7;
            transition: opacity 0.15s ease, transform 0.15s ease;
        }

        .bottom-nav-item.is-active i {
            opacity: 1;
            transform: translateY(-2px);
        }

        .bottom-nav-label {
            font-size: 11px;
            font-weight: 500;
            line-height: 1;
        }

        /* Active indicator dot */
        .bottom-nav-item.is-active::before {
            content: '';
            position: absolute;
            top: 6px;
            left: 50%;
            transform: translateX(-50%);
            width: 4px;
            height: 4px;
            background: currentColor;
            border-radius: 50%;
            opacity: 0;
            transition: opacity 0.15s ease;
        }

        .bottom-nav-item.is-active::before {
            opacity: 1;
        }

        /* Mobile theme button in header */
        .mobile-theme-btn {
            align-items: center;
            justify-content: center;
            width: 40px;
            height: 40px;
            border-radius: 10px;
            color: var(--stone, #57534e);
            background: rgba(0, 0, 0, 0.04);
            border: none;
            transition: all 0.15s ease;
            cursor: pointer;
        }

        .mobile-theme-btn:hover {
            background: rgba(0, 0, 0, 0.08);
            color: var(--ink, #1a1a1a);
        }

        html.dark .mobile-theme-btn {
            color: #94a3b8;
            background: rgba(255, 255, 255, 0.06);
        }

        html.dark .mobile-theme-btn:hover {
            background: rgba(255, 255, 255, 0.1);
            color: #f8fafc;
        }

    </style>
</head>
<body class="min-h-screen flex flex-col">

    <header class="site-header" data-header>
        <div class="max-w-6xl mx-auto px-6 lg:px-8">
            <div class="header-inner flex items-center justify-between">
                <!-- Logo -->
                <a href="<?= h(route_url('home')) ?>" class="group">
                    <span class="font-display text-xl text-ink tracking-tight">Happy Thoughts</span>
                </a>

                <!-- Desktop Navigation -->
                <nav aria-label="Primary" class="hidden md:flex items-center gap-8">
                    <?php foreach ($navItems as $route => $label): ?>
                        <a
                            href="<?= h(route_url($route)) ?>"
                            class="relative inline-flex items-center gap-2 text-sm font-medium transition-colors duration-200 <?= $currentPage === $route ? 'text-ink' : 'text-stone hover:text-ink' ?>"
                        >
                            <i data-lucide="<?= h($navIcons[$route] ?? 'circle') ?>" class="w-4 h-4"></i>
                            <?= h($label) ?>
                            <?php if ($currentPage === $route): ?>
                                <span class="absolute -bottom-1 left-0 right-0 h-0.5 bg-coral" aria-hidden="true"></span>
                            <?php endif; ?>
                        </a>
                    <?php endforeach; ?>

                    <!-- Theme Selector -->
                    <div class="theme-selector" data-theme-dropdown></div>
                </nav>

                <!-- Mobile theme toggle button -->
                <div class="md:hidden" data-mobile-theme-dropdown></div>
            </div>

        </div>
    </header>

    <?php if ($flash): ?>
        <div class="border-b border-mist bg-white">
            <div class="max-w-6xl mx-auto px-6 lg:px-8 py-4">
                <div class="flex items-center gap-3 text-sm <?= $flash['type'] === 'success' ? 'text-moss' : 'text-coral' ?>" role="status" aria-live="polite">
                    <span><?= $flash['type'] === 'success' ? '✓' : '!' ?></span>
                    <?= h($flash['message']) ?>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <main class="flex-1 pb-20 md:pb-0">

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
                } else if (theme === 'dark') {
                    html.classList.add('dark');
                } else {
                    html.classList.remove('dark');
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
