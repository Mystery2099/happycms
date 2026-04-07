<script lang="ts">
	import MobileNav from './MobileNav.svelte';
	import PrimaryNavLinks from './navigation/PrimaryNavLinks.svelte';
	import UserMenu from './UserMenu.svelte';
	import { buildPrimaryNavItems, type PrimaryNavRoute } from '../lib/navigation';

	interface Props {
		currentPage: string;
		routes: Record<PrimaryNavRoute, string>;
		isLoggedIn?: boolean;
		isAdmin?: boolean;
		userName?: string;
		userEmail?: string;
		loginUrl?: string;
		logoutUrl?: string;
		logoutCsrfToken?: string;
	}

	let {
		currentPage,
		routes,
		isLoggedIn = false,
		isAdmin = false,
		userName = '',
		userEmail = '',
		loginUrl = '/login',
		logoutUrl = '/logout',
		logoutCsrfToken = ''
	}: Props = $props();

	const navItems = $derived(buildPrimaryNavItems(isAdmin));
</script>

<header class="site-header" data-header>
	<div class="max-w-6xl mx-auto px-6 lg:px-8">
		<div class="header-inner flex items-center justify-between">
			<a href={routes.home} class="group">
				<span class="font-display text-xl text-ink tracking-tight">Happy Thoughts</span>
			</a>

			<nav aria-label="Primary" class="hidden md:flex items-center gap-8">
				<PrimaryNavLinks items={navItems} {currentPage} {routes} />

				<div class="pl-4 border-l border-mist">
					<UserMenu
						{isLoggedIn}
						{userName}
						{userEmail}
						{loginUrl}
						{logoutUrl}
						{logoutCsrfToken}
					/>
				</div>
			</nav>

		</div>
	</div>
</header>

<MobileNav
	{currentPage}
	{routes}
	{isLoggedIn}
	{isAdmin}
	{userName}
	{userEmail}
	{loginUrl}
	{logoutUrl}
	{logoutCsrfToken}
/>
