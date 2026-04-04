import './styles.css';
import { mount } from 'svelte';

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
			Check
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
					Check
				}
			});
		}
	);
}

const dashboardTarget = document.querySelector<HTMLElement>('[data-happy-dashboard]');
if (dashboardTarget) {
	import('./App.svelte').then(({ default: App }) => {
		mount(App, {
			target: dashboardTarget,
			props: {
				apiUrl: dashboardTarget.dataset.apiUrl ?? 'api/famous-thoughts.php'
			}
		});
	});
}

const thoughtControlsTarget = document.querySelector<HTMLElement>('[data-thought-controls]');
if (thoughtControlsTarget) {
	const rawCategories = thoughtControlsTarget.dataset.categories ?? '[]';
	let categories: string[] = [];

	try {
		categories = JSON.parse(rawCategories) as string[];
	} catch {
		categories = [];
	}

	import('./components/ThoughtControls.svelte').then(({ default: ThoughtControls }) => {
		mount(ThoughtControls, {
			target: thoughtControlsTarget,
			props: {
				categories
			}
		});
	});
}

// Mount FormDropdown for category selector
const categoryDropdownTarget = document.querySelector<HTMLElement>('[data-category-dropdown]');
if (categoryDropdownTarget) {
	import('./components/FormDropdown.svelte').then(({ default: FormDropdown }) => {
		const categories = JSON.parse(categoryDropdownTarget.dataset.categories ?? '[]');
		const selected = categoryDropdownTarget.dataset.selected ?? '';
		mount(FormDropdown, {
			target: categoryDropdownTarget,
			props: {
				name: 'category',
				options: categories,
				selected,
				ariaLabel: 'Category'
			}
		});
	});
}

const themeDropdownTarget = document.querySelector<HTMLElement>('[data-theme-dropdown]');
const mobileThemeDropdownTarget = document.querySelector<HTMLElement>('[data-mobile-theme-dropdown]');

if (themeDropdownTarget || mobileThemeDropdownTarget) {
	import('./components/ThemeDropdown.svelte').then(({ default: ThemeDropdown }) => {
		if (themeDropdownTarget) {
			mount(ThemeDropdown, {
				target: themeDropdownTarget,
				props: {
					variant: 'desktop'
				}
			});
		}

		if (mobileThemeDropdownTarget) {
			mount(ThemeDropdown, {
				target: mobileThemeDropdownTarget,
				props: {
					variant: 'mobile'
				}
			});
		}
	});
}
