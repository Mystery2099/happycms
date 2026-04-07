<script lang="ts">
	import { onMount } from 'svelte';
	import { on } from 'svelte/events';
	import SegmentedControl from './site/SegmentedControl.svelte';
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

	const isMobileVariant = $derived(variant === 'mobile');
	const groupClass = $derived(
		isMobileVariant
			? 'w-full'
			: 'w-full'
	);
	const buttonClass = $derived(
		isMobileVariant
			? 'min-h-11 px-2 py-2 text-xs'
			: 'px-2 py-2 text-xs'
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

<SegmentedControl
	options={[...themeOptions]}
	bind:value={theme}
	ariaLabel="Select theme"
	{groupClass}
	{buttonClass}
	onChange={handleThemeChange}
/>
