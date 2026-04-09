<script lang="ts">
	import type { Component } from 'svelte';
	import type { IconProps } from '@lucide/svelte';

	interface Props {
		icon: Component<IconProps>;
		size?: number;
		class?: string;
		strokeWidth?: number;
	}

	let { icon: IconComponent, size = 24, class: className = '', strokeWidth = 2 }: Props = $props();
</script>

<IconComponent {size} class={className} {strokeWidth} />
