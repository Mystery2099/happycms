<script lang="ts">
	import type { Component } from 'svelte';

	type IconComponent = Component<{ size?: number; class?: string }>;

	interface Props {
		title: string;
		description?: string;
		eyebrow?: string;
		level?: 'h1' | 'h2';
		icon?: IconComponent;
		className?: string;
		titleClass?: string;
		descriptionClass?: string;
		eyebrowClass?: string;
	}

	let {
		title,
		description = '',
		eyebrow = '',
		level = 'h2',
		icon,
		className = '',
		titleClass = '',
		descriptionClass = '',
		eyebrowClass = ''
	}: Props = $props();

	const Icon = $derived(icon);
</script>

<div class={['space-y-4', className]}>
	{#if eyebrow}
		<p class={['text-sm font-medium uppercase tracking-widest text-stone', eyebrowClass]}>
			{eyebrow}
		</p>
	{/if}

	<svelte:element
		this={level}
		class={[
			'font-display text-display-md text-ink',
			Icon ? 'inline-flex items-center gap-3' : '',
			titleClass
		]}
	>
		{#if Icon}
			<Icon size={24} class="text-coral" />
		{/if}
		{title}
	</svelte:element>

	{#if description}
		<p class={['text-stone leading-relaxed', descriptionClass]}>{description}</p>
	{/if}
</div>
