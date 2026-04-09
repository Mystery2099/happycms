<script lang="ts">
	import type { PrimaryNavItem, PrimaryNavRoute } from '../../lib/navigation';

	interface Props {
		items: PrimaryNavItem[];
		currentPage: string;
		routes: Record<PrimaryNavRoute, string>;
		variant?: 'desktop' | 'mobile';
	}

	let { items, currentPage, routes, variant = 'desktop' }: Props = $props();

	const isMobile = $derived(variant === 'mobile');
</script>

{#each items as item (item.key)}
	<a
		href={routes[item.key]}
		class={
			isMobile
				? ['bottom-nav-item', currentPage === item.key ? 'is-active' : '']
					: [
							'relative inline-flex items-center gap-2 rounded-sm px-2 py-1 text-sm font-medium transition-all duration-200',
							currentPage === item.key
								? 'text-ink'
								: 'text-stone hover:-translate-y-px hover:bg-coral/5 hover:text-ink'
						]
		}
		aria-current={currentPage === item.key ? 'page' : undefined}
	>
		<item.icon size={isMobile ? 20 : 16} />
		{#if isMobile}
			<span class="bottom-nav-label">{item.label}</span>
		{:else}
			{item.label}
			{#if currentPage === item.key}
				<span class="absolute -bottom-1 left-0 right-0 h-0.5 bg-coral" aria-hidden="true"></span>
			{/if}
		{/if}
	</a>
{/each}
