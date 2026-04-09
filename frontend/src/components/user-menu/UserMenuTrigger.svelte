<script lang="ts">
	import { ChevronDown, User } from '@lucide/svelte';
	import { getDesktopSurfaceTriggerClass } from '../../lib/ui-classes';

	interface Props {
		isLoggedIn: boolean;
		isOpen: boolean;
		isMobileVariant: boolean;
		userName: string;
		userEmail: string;
		triggerId: string;
		menuId?: string;
		triggerRef?: HTMLButtonElement | null;
		onclick?: () => void;
		onkeydown?: (event: KeyboardEvent) => void;
	}

	let {
		isLoggedIn,
		isOpen,
		isMobileVariant,
		userName,
		userEmail,
		triggerId,
		menuId,
		triggerRef = $bindable(),
		onclick,
		onkeydown
	}: Props = $props();

	const triggerClass = $derived(
		isMobileVariant
			? 'bottom-nav-item user-menu-trigger-mobile w-full border-0 bg-transparent shadow-none'
			: getDesktopSurfaceTriggerClass({ extraClass: 'dropdown-trigger-inline', isOpen })
	);
</script>

<button
	bind:this={triggerRef}
	type="button"
	{onclick}
	{onkeydown}
	class={triggerClass}
	id={triggerId}
	aria-controls={isOpen ? menuId : undefined}
	aria-expanded={isOpen}
	aria-label={isLoggedIn ? 'Open user menu' : 'Open site menu'}
>
	<span class={isMobileVariant ? 'flex flex-col items-center gap-1' : 'flex min-w-0 items-center gap-3'}>
		<span class="flex h-8 w-8 items-center justify-center rounded-full bg-coral/10 text-coral dark:bg-rose-400/15 dark:text-rose-300">
			<User size={16} />
		</span>
		{#if isMobileVariant}
			<span class="bottom-nav-label">{isLoggedIn ? 'Account' : 'Menu'}</span>
		{:else}
			<span class="hidden min-w-0 lg:block">
				<span class="block max-w-[120px] truncate text-sm font-medium text-ink dark:text-slate-100">
					{isLoggedIn ? userName || 'User' : 'Menu'}
				</span>
				<span class="block max-w-[140px] truncate text-xs text-stone dark:text-slate-400">
					{isLoggedIn ? userEmail || 'Preferences and account' : 'Theme and sign in'}
				</span>
			</span>
		{/if}
	</span>
	{#if !isMobileVariant}
		<ChevronDown size={16} class="shrink-0 transition-transform duration-200 {isOpen ? 'rotate-180' : ''}" />
	{/if}
</button>
