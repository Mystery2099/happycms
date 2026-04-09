import './styles.css';
import { mountPage } from './lib/mount';

mountPage('[data-home-page]', 'home-page-props', () => import('./pages/HomePage.svelte'));
mountPage('[data-thought-library-page]', 'thought-library-page-props', () => import('./pages/ThoughtLibraryPage.svelte'));
mountPage('[data-thought-form-page]', 'thought-form-page-props', () => import('./pages/ThoughtFormPage.svelte'));
mountPage('[data-delete-thought-page]', 'delete-thought-page-props', () => import('./pages/DeleteThoughtPage.svelte'));
mountPage('[data-login-page]', 'login-page-props', () => import('./pages/LoginPage.svelte'));
mountPage('[data-site-header]', 'site-header-props', () => import('./components/site/SiteHeader.svelte'));
mountPage('[data-site-footer]', 'site-footer-props', () => import('./components/site/SiteFooter.svelte'));
mountPage('[data-flash-banner]', 'flash-banner-props', () => import('./components/site/FlashBanner.svelte'));
mountPage('[data-happy-dashboard]', 'happy-dashboard-props', () => import('./components/thoughts/FamousThoughts.svelte'), (target) => ({
	apiUrl: target.dataset.apiUrl ?? 'api/famous-thoughts.php'
}));