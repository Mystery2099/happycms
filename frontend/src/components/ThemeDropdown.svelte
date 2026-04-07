<script lang="ts">
	import { onMount } from 'svelte';
	import { on } from 'svelte/events';
	import Dropdown from './Dropdown.svelte';
	import {
		getStoredTheme,
		isTheme,
		syncTheme,
		THEME_CHANGE_EVENT,
		themeOptions,
		type Theme,
		type ThemeChangeDetail
	} from '../lib/theme';
	type Variant = 'desktop' | 'mobile';

	interface Props {
		variant?: Variant;
	}

	let { variant = 'desktop' }: Props = $props();
	let theme = $state<Theme>('system');

	const isMobileTrigger = $derived(variant === 'mobile');
	const triggerClass = $derived(
		isMobileTrigger
			? 'mobile-theme-btn flex'
			: 'theme-selector-btn dropdown-trigger-inline'
	);

	function handleThemeChange(nextTheme: string) {
		if (isTheme(nextTheme)) {
			theme = nextTheme;
			syncTheme(nextTheme);
		}
	}

	onMount(() => {
		const storedTheme = getStoredTheme();
		if (storedTheme) {
			theme = storedTheme;
		}

		const handleExternalThemeChange = (event: Event) => {
			const customEvent = event as CustomEvent<ThemeChangeDetail>;
			const nextTheme = customEvent.detail?.theme;
			if (isTheme(nextTheme)) {
				theme = nextTheme;
			}
		};

		return on(window, THEME_CHANGE_EVENT, handleExternalThemeChange);
	});
</script>

<Dropdown
	options={[...themeOptions]}
	bind:value={theme}
	showIconInTrigger={true}
	showLabelInTrigger={!isMobileTrigger}
	ariaLabel="Select theme"
	listAriaLabel="Theme options"
	triggerClass={triggerClass}
	onChange={handleThemeChange}
/>
