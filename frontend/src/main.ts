import './styles.css';
import { mount } from 'svelte';
import App from './App.svelte';
import ThoughtControls from './components/ThoughtControls.svelte';
import {
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
} from 'lucide';

// Initialize Lucide icons
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

const dashboardTarget = document.querySelector<HTMLElement>('[data-happy-dashboard]');
if (dashboardTarget) {
	mount(App, {
		target: dashboardTarget,
		props: {
			apiUrl: dashboardTarget.dataset.apiUrl ?? 'api/famous-thoughts.php'
		}
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

	mount(ThoughtControls, {
		target: thoughtControlsTarget,
		props: {
			categories
		}
	});
}
