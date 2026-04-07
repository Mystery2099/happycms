<script lang="ts">
	import { FileText, Home, PlusCircle, Search, User, LogOut } from '@lucide/svelte';

	type NavRoute = 'home' | 'thoughts' | 'create' | 'search';

	interface Props {
		currentPage: string;
		routes: Record<NavRoute, string>;
		isLoggedIn?: boolean;
		isAdmin?: boolean;
		loginUrl?: string;
		logoutUrl?: string;
		logoutCsrfToken?: string;
	}

	let {
		currentPage,
		routes,
		isLoggedIn = false,
		isAdmin = false,
		loginUrl = '/login',
		logoutUrl = '/logout',
		logoutCsrfToken = ''
	}: Props = $props();

	const mobileItems = $derived([
		{ key: 'home', label: 'Home', icon: Home },
		{ key: 'thoughts', label: 'Thoughts', icon: FileText },
		...(isAdmin ? [{ key: 'create', label: 'Add', icon: PlusCircle }] : []),
		{ key: 'search', label: 'Search', icon: Search }
	] as const);
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

		{#if isLoggedIn}
			<form method="POST" action={logoutUrl} class="flex-1">
				<input type="hidden" name="csrf_token" value={logoutCsrfToken} />
				<button type="submit" class="bottom-nav-item w-full" aria-label="Sign out">
					<LogOut size={20} />
					<span class="bottom-nav-label">Sign out</span>
				</button>
			</form>
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
