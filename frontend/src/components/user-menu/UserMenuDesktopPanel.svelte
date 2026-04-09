<script lang="ts">
	import { Palette } from '@lucide/svelte';
	import ThemeSelector from '../site/ThemeSelector.svelte';
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
	<div class="space-y-4 p-4">
		<div class="rounded-lg bg-mist/35 px-4 py-4 dark:bg-slate-800/60">
			<div class="mb-3 flex items-center gap-2 text-xs font-semibold uppercase tracking-[0.18em] text-stone dark:text-slate-400">
				<Palette size={14} />
				Theme
			</div>
			<ThemeSelector variant="desktop" />
		</div>

		<UserMenuAccountCard {isLoggedIn} {userName} {userEmail} variant="desktop" />

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
