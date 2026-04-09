<script lang="ts">
	import type { Snippet } from 'svelte';

	interface Props {
		title: string;
		description?: string;
		children?: Snippet;
		className?: string;
	}

	let { title, description = '', children, className = '' }: Props = $props();
</script>

<div class={['border-mist border border-dashed rounded-lg py-16 text-center', className]}>
	<p class="font-display text-xl text-ink mb-2">{title}</p>
	{#if description}
		<p class="text-stone mb-6">{description}</p>
	{/if}
	{#if children}
		{@render children()}
	{/if}
</div>
