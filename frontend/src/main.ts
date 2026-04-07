import './styles.css';
import { mount } from 'svelte';
import { mountPage } from './lib/mount';

if (document.querySelector('[data-lucide]')) {
	import('lucide').then(
		({
			createIcons,
			Monitor,
			Sun,
			Moon,
			ChevronDown,
			Menu,
			X,
			Home,
			FileText,
			PlusCircle,
			Search,
			Image,
			Pencil,
			Trash2,
			ChevronLeft,
			AlertTriangle,
			ArrowRight,
			MapPin,
			Save,
			BadgeX,
			Type,
			User,
			Tag,
			SmilePlus,
			MessageSquareText,
			CalendarDays,
			Hash,
			Star,
			Check,
			LogIn,
			Lock,
			Eye,
			EyeOff,
			AlertCircle,
			LogOut,
			Settings
		}) => {
			createIcons({
				icons: {
					Monitor,
					Sun,
					Moon,
					ChevronDown,
					Menu,
					X,
					Home,
					FileText,
					PlusCircle,
					Search,
					Image,
					Pencil,
					Trash2,
					ChevronLeft,
					AlertTriangle,
					ArrowRight,
					MapPin,
					Save,
					BadgeX,
					Type,
					User,
					Tag,
					SmilePlus,
					MessageSquareText,
					CalendarDays,
					Hash,
					Star,
					Check,
					LogIn,
					Lock,
					Eye,
					EyeOff,
					AlertCircle,
					LogOut,
					Settings
				}
			});
		}
	);
}

mountPage('[data-home-page]', 'home-page-props', () => import('./pages/HomePage.svelte'));
mountPage('[data-thought-library-page]', 'thought-library-page-props', () => import('./pages/ThoughtLibraryPage.svelte'));
mountPage('[data-thought-form-page]', 'thought-form-page-props', () => import('./pages/ThoughtFormPage.svelte'));
mountPage('[data-delete-thought-page]', 'delete-thought-page-props', () => import('./pages/DeleteThoughtPage.svelte'));
mountPage('[data-login-page]', 'login-page-props', () => import('./pages/LoginPage.svelte'));
mountPage('[data-site-header]', 'site-header-props', () => import('./components/SiteHeader.svelte'));
mountPage('[data-site-footer]', 'site-footer-props', () => import('./components/SiteFooter.svelte'));
mountPage('[data-flash-banner]', 'flash-banner-props', () => import('./components/FlashBanner.svelte'));

const dashboardTarget = document.querySelector<HTMLElement>('[data-happy-dashboard]');
if (dashboardTarget) {
	import('./components/FamousThoughts.svelte').then(({ default: FamousThoughts }) => {
		mount(FamousThoughts, {
			target: dashboardTarget,
			props: {
				apiUrl: dashboardTarget.dataset.apiUrl ?? 'api/famous-thoughts.php'
			}
		});
	});
}
