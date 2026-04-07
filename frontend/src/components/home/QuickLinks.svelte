<script lang="ts">
	import { ArrowRight } from '@lucide/svelte';
	import type { IconComponent } from '../../lib/types';

	type QuickLink = {
		href: string | null;
		label: string;
		icon: IconComponent;
	};

	interface Props {
		items: QuickLink[];
	}

	let { items }: Props = $props();
</script>

<nav class="space-y-3" aria-label="Primary actions">
	{#each items as item (item.label)}
		{#if item.href}
			<a href={item.href} class="group border-mist flex items-center justify-between border-b py-3">
				<span class="text-ink inline-flex items-center gap-3 transition-colors group-hover:text-coral">
					<item.icon size={16} />
					{item.label}
				</span>
				<ArrowRight size={16} class="text-stone" />
			</a>
		{/if}
	{/each}
</nav>
