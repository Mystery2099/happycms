<script lang="ts">
	import { Palette } from '@lucide/svelte';
	import ThemeDropdown from '../ThemeDropdown.svelte';
	import UserMenuAccountCard from './UserMenuAccountCard.svelte';
	import UserMenuAuthAction from './UserMenuAuthAction.svelte';

	interface Props {
		menuId: string;
		triggerId: string;
		panelClass: string;
		isLoggedIn: boolean;
		userName: string;
		userEmail: string;
		loginUrl: string;
		logoutUrl: string;
		logoutCsrfToken?: string;
		firstActionRef?: HTMLAnchorElement | HTMLButtonElement | null;
		onNavigate?: () => void;
	}

	let {
		menuId,
		triggerId,
		panelClass,
		isLoggedIn,
		userName,
		userEmail,
		loginUrl,
		logoutUrl,
		logoutCsrfToken = '',
		firstActionRef = $bindable(),
		onNavigate
	}: Props = $props();
</script>

<div id={menuId} class={panelClass} aria-labelledby={triggerId}>
	<UserMenuAccountCard {isLoggedIn} {userName} {userEmail} variant="desktop" />

	<div class="space-y-4 p-3">
		<div class="rounded-xl bg-mist/35 px-3 py-3 dark:bg-slate-800/60">
			<div class="mb-2 flex items-center gap-2 text-xs font-semibold uppercase tracking-[0.18em] text-stone dark:text-slate-400">
				<Palette size={14} />
				Theme
			</div>
			<ThemeDropdown variant="desktop" />
		</div>

		<UserMenuAuthAction
			{isLoggedIn}
			{loginUrl}
			{logoutUrl}
			{logoutCsrfToken}
			variant="desktop"
			bind:firstActionRef
			{onNavigate}
		/>
	</div>
</div>
