<script lang="ts">
	import type { PrimaryNavRoute } from '../lib/navigation';
	import { buildPrimaryNavItems } from '../lib/navigation';
	import PrimaryNavLinks from './navigation/PrimaryNavLinks.svelte';
	import UserMenu from './UserMenu.svelte';

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

	const mobileItems = $derived(buildPrimaryNavItems(isAdmin, true));
</script>

<nav class="bottom-nav md:hidden" aria-label="Mobile navigation">
	<div class="bottom-nav-items">
		<PrimaryNavLinks items={mobileItems} {currentPage} {routes} variant="mobile" />

		<div class="flex-1">
			<UserMenu
				{isLoggedIn}
				{userName}
				{userEmail}
				variant="mobile"
				{loginUrl}
				{logoutUrl}
				{logoutCsrfToken}
			/>
		</div>
	</div>
</nav>
