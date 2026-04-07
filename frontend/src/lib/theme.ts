import { Monitor, Moon, Sun } from '@lucide/svelte';

export type Theme = 'system' | 'light' | 'dark';
export type ThemeChangeDetail = {
	theme?: Theme;
};

export const THEME_STORAGE_KEY = 'happy-thoughts-theme';
export const THEME_CHANGE_EVENT = 'happy-theme-change';

export const themeOptions = [
	{ value: 'system', label: 'System', icon: Monitor },
	{ value: 'light', label: 'Light', icon: Sun },
	{ value: 'dark', label: 'Dark', icon: Moon }
] as const;

export function isTheme(value: string | null | undefined): value is Theme {
	return value === 'system' || value === 'light' || value === 'dark';
}

export function applyThemeToDocument(nextTheme: Theme) {
	const html = document.documentElement;

	if (nextTheme === 'system') {
		const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
		html.classList.toggle('dark', prefersDark);
		html.style.colorScheme = prefersDark ? 'dark' : 'light';
		return;
	}

	html.classList.toggle('dark', nextTheme === 'dark');
	html.style.colorScheme = nextTheme;
}

export function dispatchThemeChange(nextTheme: Theme) {
	window.dispatchEvent(
		new CustomEvent<ThemeChangeDetail>(THEME_CHANGE_EVENT, {
			detail: { theme: nextTheme }
		})
	);
}

export function syncTheme(nextTheme: Theme) {
	applyThemeToDocument(nextTheme);
	localStorage.setItem(THEME_STORAGE_KEY, nextTheme);
	dispatchThemeChange(nextTheme);
}

export function getStoredTheme(): Theme | null {
	const storedTheme = localStorage.getItem(THEME_STORAGE_KEY);
	return isTheme(storedTheme) ? storedTheme : null;
}
