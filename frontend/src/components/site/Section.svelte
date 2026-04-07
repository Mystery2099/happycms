<script lang="ts">
	import type { Snippet } from 'svelte';

	interface Props {
		children: Snippet;
		padded?: boolean;
		sectionClass?: string;
		containerClass?: string;
		widthClass?: string;
	}

	let {
		children,
		padded = true,
		sectionClass = '',
		containerClass = '',
		widthClass = 'max-w-6xl'
	}: Props = $props();
</script>

<section class={[padded ? 'section-padding' : '', sectionClass]}>
	<div class={[widthClass, 'mx-auto px-6 lg:px-8', containerClass]}>
		{@render children()}
	</div>
</section>
