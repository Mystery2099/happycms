<script lang="ts">
	import { FileText, Home, PlusCircle, Search } from '@lucide/svelte';
	import ThemeDropdown from './ThemeDropdown.svelte';

	type NavRoute = 'home' | 'thoughts' | 'create' | 'search';

	interface Props {
		currentPage: string;
		routes: Record<NavRoute, string>;
	}

	let { currentPage, routes }: Props = $props();

	const navItems = [
		{ key: 'home', label: 'Home', icon: Home },
		{ key: 'thoughts', label: 'Thoughts', icon: FileText },
		{ key: 'create', label: 'Add Thought', icon: PlusCircle },
		{ key: 'search', label: 'Search', icon: Search }
	] as const;
</script>

<header class="site-header" data-header>
	<div class="max-w-6xl mx-auto px-6 lg:px-8">
		<div class="header-inner flex items-center justify-between">
			<a href={routes.home} class="group">
				<span class="font-display text-xl text-ink tracking-tight">Happy Thoughts</span>
			</a>

			<nav aria-label="Primary" class="hidden md:flex items-center gap-8">
				{#each navItems as item (item.key)}
					<a
						href={routes[item.key]}
						class={[
							'relative inline-flex items-center gap-2 text-sm font-medium transition-colors duration-200',
							currentPage === item.key ? 'text-ink' : 'text-stone hover:text-ink'
						]}
						aria-current={currentPage === item.key ? 'page' : undefined}
					>
						<item.icon size={16} />
						{item.label}
						{#if currentPage === item.key}
							<span class="absolute -bottom-1 left-0 right-0 h-0.5 bg-coral" aria-hidden="true"></span>
						{/if}
					</a>
				{/each}

				<div class="theme-selector">
					<ThemeDropdown variant="desktop" />
				</div>
			</nav>

			<div class="md:hidden">
				<ThemeDropdown variant="mobile" />
			</div>
		</div>
	</div>
</header>
