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
						'group relative inline-flex items-center gap-2 rounded-sm px-2 py-1 text-sm font-medium transition-[color,background-color,transform] duration-200 ease-enter motion-reduce:transition-none',
						currentPage === item.key
							? 'text-ink'
							: 'text-stone hover:-translate-y-px hover:bg-coral-soft hover:text-ink'
					]
	}
	aria-current={currentPage === item.key ? 'page' : undefined}
>
	<item.icon size={isMobile ? 20 : 16} />
	{#if isMobile}
		<span class="bottom-nav-label">{item.label}</span>
	{:else}
		{item.label}
		<span
			class={['absolute bottom-0 left-0 right-0 h-0.5 bg-coral origin-center transition-transform duration-200 ease-enter motion-reduce:transition-none', currentPage === item.key ? 'scale-x-100' : 'scale-x-0 group-hover:scale-x-50']}
			aria-hidden="true"
		></span>
	{/if}
</a>
{/each}