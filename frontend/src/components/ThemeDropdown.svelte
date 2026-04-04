<script lang="ts">
	import { Monitor, Moon, Sun } from '@lucide/svelte';
	import { onMount } from 'svelte';
	import Dropdown from './Dropdown.svelte';

	type Theme = 'system' | 'light' | 'dark';
	type Variant = 'desktop' | 'mobile';

	type ThemeChangeDetail = {
		theme?: Theme;
	};

	interface Props {
		variant?: Variant;
	}

	const THEME_STORAGE_KEY = 'happy-thoughts-theme';

	const options = [
		{ value: 'system', label: 'System', icon: Monitor },
		{ value: 'light', label: 'Light', icon: Sun },
		{ value: 'dark', label: 'Dark', icon: Moon }
	] as const;

	let { variant = 'desktop' }: Props = $props();
	let theme = $state<Theme>('system');

	const isMobileTrigger = $derived(variant === 'mobile');
	const triggerClass = $derived(
		isMobileTrigger
			? 'mobile-theme-btn flex'
			: 'theme-selector-btn'
	);

	function applyThemeToDocument(nextTheme: Theme) {
		const html = document.documentElement;

		if (nextTheme === 'system') {
			const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
			html.classList.toggle('dark', prefersDark);
			return;
		}

		html.classList.toggle('dark', nextTheme === 'dark');
	}

	function syncTheme(nextTheme: Theme) {
		theme = nextTheme;
		applyThemeToDocument(nextTheme);
		localStorage.setItem(THEME_STORAGE_KEY, nextTheme);
		window.dispatchEvent(
			new CustomEvent<ThemeChangeDetail>('happy-theme-change', {
				detail: { theme: nextTheme }
			})
		);
	}

	function handleThemeChange(nextTheme: string) {
		if (nextTheme === 'system' || nextTheme === 'light' || nextTheme === 'dark') {
			syncTheme(nextTheme);
		}
	}

	onMount(() => {
		const storedTheme = localStorage.getItem(THEME_STORAGE_KEY);
		if (storedTheme === 'system' || storedTheme === 'light' || storedTheme === 'dark') {
			theme = storedTheme;
		}

		const handleExternalThemeChange = (event: Event) => {
			const customEvent = event as CustomEvent<ThemeChangeDetail>;
			const nextTheme = customEvent.detail?.theme;
			if (nextTheme === 'system' || nextTheme === 'light' || nextTheme === 'dark') {
				theme = nextTheme;
			}
		};

		window.addEventListener('happy-theme-change', handleExternalThemeChange);

		return () => {
			window.removeEventListener('happy-theme-change', handleExternalThemeChange);
		};
	});
</script>

<Dropdown
	options={[...options]}
	bind:value={theme}
	showIconInTrigger={true}
	showLabelInTrigger={!isMobileTrigger}
	ariaLabel="Select theme"
	listAriaLabel="Theme options"
	triggerClass={triggerClass}
	menuClass="theme-dropdown theme-dropdown--mounted"
	optionClass="theme-option"
	onChange={handleThemeChange}
/>
