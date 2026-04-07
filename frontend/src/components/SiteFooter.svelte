<script lang="ts">
	import { FileText, Home, PlusCircle, Search, User, LogOut } from '@lucide/svelte';

	type NavRoute = 'home' | 'thoughts' | 'create' | 'search' | 'login';

	interface Props {
		currentPage: string;
		routes: Record<NavRoute, string>;
		// Backend: Add auth props when ready
		// isLoggedIn?: boolean;
		// userName?: string;
		// loginUrl?: string;
		// logoutUrl?: string;
	}

	let { 
		currentPage, 
		routes,
		// Backend: Destructure auth props when ready
		// isLoggedIn = false,
		// userName = '',
		// loginUrl = '/login',
		// logoutUrl = '/logout'
	}: Props = $props();

	// Backend: Remove this placeholder when auth is implemented
	const isLoggedIn = false;
	const loginUrl = '/login';
	const logoutUrl = '/logout';

	const mobileItems = [
		{ key: 'home', label: 'Home', icon: Home },
		{ key: 'thoughts', label: 'Thoughts', icon: FileText },
		{ key: 'create', label: 'Add', icon: PlusCircle },
		{ key: 'search', label: 'Search', icon: Search }
	] as const;

	function handleMobileLogout() {
		// Form submission to backend logout endpoint
		const form = document.createElement('form');
		form.method = 'POST';
		form.action = logoutUrl;
		document.body.appendChild(form);
		form.submit();
		document.body.removeChild(form);
	}
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
		
		<!-- Account/Auth Mobile Item -->
		{#if isLoggedIn}
			<button
				type="button"
				onclick={handleMobileLogout}
				class="bottom-nav-item"
				aria-label="Sign out"
			>
				<LogOut size={20} />
				<span class="bottom-nav-label">Sign out</span>
			</button>
		{:else}
			<a
				href={loginUrl}
				class={['bottom-nav-item', currentPage === 'login' ? 'is-active' : '']}
				aria-current={currentPage === 'login' ? 'page' : undefined}
			>
				<User size={20} />
				<span class="bottom-nav-label">Sign in</span>
			</a>
		{/if}
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
