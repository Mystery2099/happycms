<script lang="ts">
	import { FileText, Home, PlusCircle, Search } from '@lucide/svelte';

	type NavRoute = 'home' | 'thoughts' | 'create' | 'search';

	interface Props {
		currentPage: string;
		routes: Record<NavRoute, string>;
	}

	let { currentPage, routes }: Props = $props();

	const mobileItems = [
		{ key: 'home', label: 'Home', icon: Home },
		{ key: 'thoughts', label: 'Thoughts', icon: FileText },
		{ key: 'create', label: 'Add', icon: PlusCircle },
		{ key: 'search', label: 'Search', icon: Search }
	] as const;
</script>

<nav class="bottom-nav md:hidden" aria-label="Mobile navigation">
	<div class="bottom-nav-items">
		{#each mobileItems as item (item.key)}
			<a
				href={routes[item.key]}
				class={['bottom-nav-item', currentPage === item.key ? 'is-active' : '']}
				aria-current={currentPage === item.key ? 'page' : undefined}
			>
				<item.icon size={20} />
				<span class="bottom-nav-label">{item.label}</span>
			</a>
		{/each}
	</div>
</nav>

<footer class="border-t border-mist mt-auto hidden md:block">
	<div class="max-w-6xl mx-auto px-6 py-12 lg:px-8">
		<div class="flex flex-col gap-6 md:flex-row md:items-center md:justify-between">
			<div>
				<p class="font-display text-lg text-ink">Happy Thoughts</p>
				<p class="text-sm text-stone mt-1">Collecting moments of joy since 2026</p>
			</div>
			<div class="flex items-center gap-6 text-sm text-stone">
				<span>PHP + SQLite</span>
				<span class="text-mist">|</span>
				<span>Svelte + TypeScript</span>
				<span class="text-mist">|</span>
				<span>Tailwind CSS</span>
			</div>
		</div>
	</div>
</footer>
