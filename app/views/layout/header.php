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

        /* Mobile nav drawer */
        .mobile-drawer-overlay {
            position: fixed;
            inset: 0;
            background: rgba(0, 0, 0, 0);
            backdrop-filter: blur(0px);
            -webkit-backdrop-filter: blur(0px);
            z-index: 998;
            pointer-events: none;
            transition: 
                background-color 0.3s ease,
                backdrop-filter 0.3s ease;
        }

        .mobile-drawer-overlay.is-visible {
            background: rgba(0, 0, 0, 0.4);
            backdrop-filter: blur(4px);
            -webkit-backdrop-filter: blur(4px);
            pointer-events: auto;
        }

        .mobile-drawer {
            position: fixed;
            left: 0;
            right: 0;
            bottom: 0;
            z-index: 999;
            background: #faf9f7;
            border-radius: 24px 24px 0 0;
            transform: translateY(100%);
            transition: transform 0.35s cubic-bezier(0.32, 0.72, 0, 1);
            max-height: 85vh;
            overflow-y: auto;
            box-shadow: 0 -4px 30px rgba(0, 0, 0, 0.15);
        }

        .mobile-drawer.is-open {
            transform: translateY(0);
        }

        html.dark .mobile-drawer {
            background: #0f172a;
        }

        .mobile-drawer-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 16px 20px 12px;
            border-bottom: 1px solid rgba(231, 229, 228, 0.6);
        }

        html.dark .mobile-drawer-header {
            border-color: rgba(51, 65, 85, 0.5);
        }

        .mobile-drawer-handle {
            position: absolute;
            top: 8px;
            left: 50%;
            transform: translateX(-50%);
            width: 36px;
            height: 4px;
            background: rgba(0, 0, 0, 0.2);
            border-radius: 2px;
        }

        html.dark .mobile-drawer-handle {
            background: rgba(255, 255, 255, 0.2);
        }

        .mobile-drawer-nav {
            padding: 8px 16px;
        }

        .mobile-drawer-nav a {
            display: flex;
            align-items: center;
            gap: 16px;
            padding: 16px;
            font-size: 17px;
            font-weight: 500;
            color: var(--stone, #57534e);
            border-radius: 12px;
            transition: all 0.15s ease;
            text-decoration: none;
        }

        .mobile-drawer-nav a:hover,
        .mobile-drawer-nav a:active {
            background: rgba(0, 0, 0, 0.05);
            color: var(--ink, #1a1a1a);
        }

        html.dark .mobile-drawer-nav a {
            color: #94a3b8;
        }

        html.dark .mobile-drawer-nav a:hover,
        html.dark .mobile-drawer-nav a:active {
            background: rgba(255, 255, 255, 0.08);
            color: #f8fafc;
        }

        .mobile-drawer-nav a.is-active {
            background: rgba(220, 95, 80, 0.12);
            color: #dc5f50;
        }

        html.dark .mobile-drawer-nav a.is-active {
            background: rgba(251, 113, 133, 0.15);
            color: #fb7185;
        }

        .mobile-drawer-nav a svg {
            flex-shrink: 0;
            opacity: 0.7;
        }

        .mobile-drawer-footer {
            padding: 12px 20px 24px;
            border-top: 1px solid rgba(231, 229, 228, 0.6);
        }

        html.dark .mobile-drawer-footer {
            border-color: rgba(51, 65, 85, 0.5);
        }

        .mobile-drawer-close {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 40px;
            height: 40px;
            border-radius: 10px;
            color: var(--stone, #57534e);
            background: transparent;
            border: none;
            transition: all 0.15s ease;
            cursor: pointer;
        }

        .mobile-drawer-close:hover {
            background: rgba(0, 0, 0, 0.05);
            color: var(--ink, #1a1a1a);
        }

        html.dark .mobile-drawer-close {
            color: #94a3b8;
        }

        html.dark .mobile-drawer-close:hover {
            background: rgba(255, 255, 255, 0.08);
            color: #f8fafc;
        }

        .mobile-drawer-theme {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 12px 4px;
            font-size: 15px;
            font-weight: 500;
            color: var(--stone, #57534e);
            background: transparent;
            border: none;
            width: 100%;
            cursor: pointer;
        }

        html.dark .mobile-drawer-theme {
            color: #94a3b8;
        }

        .mobile-drawer-theme-options {
            display: flex;
            gap: 12px;
        }

        .mobile-drawer-theme-option {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 44px;
            height: 44px;
            border-radius: 10px;
            background: rgba(0, 0, 0, 0.04);
            border: 2px solid transparent;
            color: var(--stone, #57534e);
            transition: all 0.15s ease;
            cursor: pointer;
        }

        .mobile-drawer-theme-option:hover {
            background: rgba(0, 0, 0, 0.08);
        }

        .mobile-drawer-theme-option.is-active {
            border-color: #dc5f50;
            background: rgba(220, 95, 80, 0.1);
            color: #dc5f50;
        }

        html.dark .mobile-drawer-theme-option {
            background: rgba(255, 255, 255, 0.06);
            color: #94a3b8;
        }

        html.dark .mobile-drawer-theme-option:hover {
            background: rgba(255, 255, 255, 0.1);
        }

        html.dark .mobile-drawer-theme-option.is-active {
            border-color: #fb7185;
            background: rgba(251, 113, 133, 0.15);
            color: #fb7185;
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

        .theme-dropdown.is-open {
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
            display: flex;
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

        /* Preferences Drawer Content */
        .mobile-drawer-content {
            padding: 8px 16px 24px;
        }

        .preferences-section {
            margin-bottom: 24px;
        }

        .preferences-section-title {
            font-size: 13px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: var(--stone, #57534e);
            margin-bottom: 12px;
            padding-left: 12px;
        }

        html.dark .preferences-section-title {
            color: #94a3b8;
        }

        .preferences-options {
            display: flex;
            flex-direction: column;
            gap: 4px;
        }

        .preferences-option {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 12px;
            border-radius: 12px;
            background: transparent;
            border: none;
            color: var(--ink, #1a1a1a);
            cursor: pointer;
            transition: all 0.15s ease;
            text-align: left;
            width: 100%;
        }

        .preferences-option:hover {
            background: rgba(0, 0, 0, 0.04);
        }

        html.dark .preferences-option {
            color: #f8fafc;
        }

        html.dark .preferences-option:hover {
            background: rgba(255, 255, 255, 0.06);
        }

        .preferences-option.is-active {
            background: rgba(220, 95, 80, 0.1);
        }

        html.dark .preferences-option.is-active {
            background: rgba(251, 113, 133, 0.15);
        }

        .preferences-option-icon {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 36px;
            height: 36px;
            border-radius: 10px;
            background: rgba(0, 0, 0, 0.04);
            color: var(--stone, #57534e);
        }

        html.dark .preferences-option-icon {
            background: rgba(255, 255, 255, 0.06);
            color: #94a3b8;
        }

        .preferences-option.is-active .preferences-option-icon {
            background: rgba(220, 95, 80, 0.15);
            color: #dc5f50;
        }

        html.dark .preferences-option.is-active .preferences-option-icon {
            background: rgba(251, 113, 133, 0.2);
            color: #fb7185;
        }

        .preferences-option-label {
            flex: 1;
            font-size: 15px;
            font-weight: 500;
        }

        .preferences-option-check {
            opacity: 0;
            color: #dc5f50;
            transition: opacity 0.15s ease;
        }

        html.dark .preferences-option-check {
            color: #fb7185;
        }

        .preferences-option.is-active .preferences-option-check {
            opacity: 1;
        }

    </style>
</head>
<body class="min-h-screen flex flex-col">

    <header class="site-header" data-header>
        <div class="max-w-6xl mx-auto px-6 lg:px-8">
            <div class="header-inner flex items-center justify-between">
                <!-- Logo -->
                <a href="<?= h(route_url('home')) ?>" class="flex items-center gap-3 group">
                    <span class="flex h-10 w-10 items-center justify-center rounded-full bg-coral/10 text-coral transition-transform duration-200 group-hover:scale-110">
                        <i data-lucide="sun" class="w-5 h-5"></i>
                    </span>
                    <div>
                        <span class="font-display text-xl text-ink tracking-tight">Happy Thoughts</span>
                    </div>
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
                    <div class="theme-selector" data-theme-selector>
                        <button class="theme-selector-btn" aria-label="Select theme" aria-expanded="false" aria-haspopup="menu">
                            <span data-theme-icon-system class="theme-btn-icon">
                                <i data-lucide="monitor" class="w-4 h-4"></i>
                            </span>
                            <span data-theme-icon-sun class="theme-btn-icon hidden">
                                <i data-lucide="sun" class="w-4 h-4"></i>
                            </span>
                            <span data-theme-icon-moon class="theme-btn-icon hidden">
                                <i data-lucide="moon" class="w-4 h-4"></i>
                            </span>
                            <span data-theme-label>System</span>
                            <i data-lucide="chevron-down" class="w-3.5 h-3.5"></i>
                        </button>
                        <div class="theme-dropdown" role="menu" aria-label="Theme options">
                            <button class="theme-option" data-theme-value="system" role="menuitem" aria-checked="true">
                                <i data-lucide="monitor" class="w-4 h-4"></i>
                                System
                            </button>
                            <button class="theme-option" data-theme-value="light" role="menuitem" aria-checked="false">
                                <i data-lucide="sun" class="w-4 h-4"></i>
                                Light
                            </button>
                            <button class="theme-option" data-theme-value="dark" role="menuitem" aria-checked="false">
                                <i data-lucide="moon" class="w-4 h-4"></i>
                                Dark
                            </button>
                        </div>
                    </div>
                </nav>

                <!-- Mobile theme toggle button -->
                <button
                    type="button"
                    class="mobile-theme-btn flex md:hidden"
                    aria-label="Toggle theme"
                    data-mobile-theme-toggle
                >
                    <span data-theme-icon-system><i data-lucide="monitor" class="w-5 h-5"></i></span>
                    <span data-theme-icon-sun class="hidden"><i data-lucide="sun" class="w-5 h-5"></i></span>
                    <span data-theme-icon-moon class="hidden"><i data-lucide="moon" class="w-5 h-5"></i></span>
                </button>
            </div>

        </div>
    </header>

    <!-- Mobile Preferences Drawer -->
    <div class="mobile-drawer-overlay md:hidden" data-preferences-overlay aria-hidden="true"></div>
    <div class="mobile-drawer md:hidden" data-preferences-drawer id="preferences-drawer" aria-hidden="true" inert>
        <div class="mobile-drawer-handle" aria-hidden="true"></div>
        <div class="mobile-drawer-header">
            <span class="font-display text-xl text-ink">Preferences</span>
            <button class="mobile-drawer-close" data-preferences-close aria-label="Close preferences">
                <i data-lucide="x" class="w-6 h-6"></i>
            </button>
        </div>
        <div class="mobile-drawer-content">
            <div class="preferences-section">
                <h3 class="preferences-section-title">Appearance</h3>
                <div class="preferences-options">
                    <button class="preferences-option" data-pref-theme="system">
                        <span class="preferences-option-icon">
                            <i data-lucide="monitor" class="w-5 h-5"></i>
                        </span>
                        <span class="preferences-option-label">System</span>
                        <span class="preferences-option-check" data-check-system>
                            <i data-lucide="check" class="w-4 h-4"></i>
                        </span>
                    </button>
                    <button class="preferences-option" data-pref-theme="light">
                        <span class="preferences-option-icon">
                            <i data-lucide="sun" class="w-5 h-5"></i>
                        </span>
                        <span class="preferences-option-label">Light</span>
                        <span class="preferences-option-check" data-check-light>
                            <i data-lucide="check" class="w-4 h-4"></i>
                        </span>
                    </button>
                    <button class="preferences-option" data-pref-theme="dark">
                        <span class="preferences-option-icon">
                            <i data-lucide="moon" class="w-5 h-5"></i>
                        </span>
                        <span class="preferences-option-label">Dark</span>
                        <span class="preferences-option-check" data-check-dark>
                            <i data-lucide="check" class="w-4 h-4"></i>
                        </span>
                    </button>
                </div>
            </div>
        </div>
    </div>

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
            const themeLabels = { system: 'System', light: 'Light', dark: 'Dark' };
            
            // Get saved theme or default to system
            let currentTheme = localStorage.getItem(THEME_STORAGE_KEY) || 'system';
            
            // Apply theme to document
            function applyTheme(theme) {
                const html = document.documentElement;
                
                if (theme === 'system') {
                    const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
                    html.classList.toggle('dark', prefersDark);
                } else if (theme === 'dark') {
                    html.classList.add('dark');
                } else {
                    html.classList.remove('dark');
                }
                
                currentTheme = theme;
                localStorage.setItem(THEME_STORAGE_KEY, theme);
                updateThemeUI();
            }
            
            // Update UI elements
            function updateThemeUI() {
                // Update desktop selector label
                const themeLabel = document.querySelector('[data-theme-label]');
                if (themeLabel) themeLabel.textContent = themeLabels[currentTheme];

                // Update desktop dropdown options
                document.querySelectorAll('[data-theme-value]').forEach(btn => {
                    const isActive = btn.getAttribute('data-theme-value') === currentTheme;
                    btn.classList.toggle('is-active', isActive);
                    btn.setAttribute('aria-checked', isActive.toString());
                    btn.setAttribute('aria-selected', isActive.toString());
                });

                // Update header icons (both desktop and mobile)
                const iconMap = {
                    'system': document.querySelectorAll('[data-theme-icon-system]'),
                    'light': document.querySelectorAll('[data-theme-icon-sun]'),
                    'dark': document.querySelectorAll('[data-theme-icon-moon]')
                };

                Object.values(iconMap).flat().forEach(el => el?.classList.add('hidden'));
                iconMap[currentTheme]?.forEach(el => el?.classList.remove('hidden'));

                // Update preferences drawer options
                document.querySelectorAll('[data-pref-theme]').forEach(btn => {
                    const isActive = btn.getAttribute('data-pref-theme') === currentTheme;
                    btn.classList.toggle('is-active', isActive);
                });
            }
            
            // Desktop theme dropdown
            const themeSelector = document.querySelector('[data-theme-selector]');
            const themeBtn = themeSelector?.querySelector('button');
            const themeDropdown = themeSelector?.querySelector('.theme-dropdown');

            if (themeBtn && themeDropdown) {
                const closeDropdown = () => {
                    themeDropdown.classList.remove('is-open');
                    themeBtn.setAttribute('aria-expanded', 'false');
                };

                themeBtn.addEventListener('click', (e) => {
                    e.stopPropagation();
                    const willOpen = !themeDropdown.classList.contains('is-open');
                    themeDropdown.classList.toggle('is-open', willOpen);
                    themeBtn.setAttribute('aria-expanded', willOpen.toString());
                });

                document.addEventListener('click', closeDropdown);

                themeDropdown.querySelectorAll('[data-theme-value]').forEach(btn => {
                    btn.addEventListener('click', () => {
                        applyTheme(btn.getAttribute('data-theme-value'));
                        closeDropdown();
                    });
                });
            }
            
            // Mobile Preferences Drawer
            const prefToggle = document.querySelector('[data-mobile-theme-toggle]');
            const prefDrawer = document.querySelector('[data-preferences-drawer]');
            const prefOverlay = document.querySelector('[data-preferences-overlay]');
            const prefClose = document.querySelector('[data-preferences-close]');
            const isOpen = () => prefDrawer?.classList.contains('is-open');

            function setDrawerState(open) {
                prefDrawer?.classList.toggle('is-open', open);
                prefDrawer?.setAttribute('aria-hidden', (!open).toString());
                open ? prefDrawer?.removeAttribute('inert') : prefDrawer?.setAttribute('inert', '');

                prefOverlay?.classList.toggle('is-visible', open);
                prefOverlay?.setAttribute('aria-hidden', (!open).toString());

                prefToggle?.setAttribute('aria-expanded', open.toString());
                document.body.style.overflow = open ? 'hidden' : '';
            }

            function openPreferences() {
                setDrawerState(true);
                prefDrawer?.querySelector('button[data-pref-theme]')?.focus();
            }

            function closePreferences() {
                setDrawerState(false);
                prefToggle?.focus();
            }

            prefToggle?.addEventListener('click', openPreferences);
            prefClose?.addEventListener('click', closePreferences);
            prefOverlay?.addEventListener('click', closePreferences);

            // Handle theme selection from preferences drawer
            document.querySelectorAll('[data-pref-theme]').forEach(btn => {
                btn.addEventListener('click', () => applyTheme(btn.getAttribute('data-pref-theme')));
            });

            // Close preferences on Escape key
            document.addEventListener('keydown', (e) => {
                if (e.key === 'Escape' && isOpen()) closePreferences();
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
