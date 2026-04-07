<script lang="ts">
	import { LogIn, LogOut } from '@lucide/svelte';

	interface Props {
		isLoggedIn: boolean;
		loginUrl: string;
		logoutUrl: string;
		logoutCsrfToken?: string;
		variant?: 'desktop' | 'mobile';
		firstActionRef?: HTMLAnchorElement | HTMLButtonElement | null;
		onNavigate?: () => void;
	}

	let {
		isLoggedIn,
		loginUrl,
		logoutUrl,
		logoutCsrfToken = '',
		variant = 'desktop',
		firstActionRef = $bindable(),
		onNavigate
	}: Props = $props();

	const isDesktop = $derived(variant === 'desktop');
	const sharedClass =
		'flex w-full items-center gap-3 rounded-2xl border border-stone-200/80 bg-white/80 px-4 py-3 text-left text-sm font-medium transition-colors dark:border-slate-700 dark:bg-slate-800/90 dark:text-slate-100';
</script>

{#if isLoggedIn}
	<form method="POST" action={logoutUrl}>
		<input type="hidden" name="csrf_token" value={logoutCsrfToken} />
		<button
			bind:this={firstActionRef}
			type="submit"
			class={
				isDesktop
					? `${sharedClass} min-h-[3.25rem] text-ink hover:border-red-200 hover:bg-red-50 hover:text-red-600 dark:hover:border-rose-400/30 dark:hover:bg-rose-400/10 dark:hover:text-rose-300`
					: `${sharedClass} min-h-14 text-ink hover:border-red-200 hover:bg-red-50 hover:text-red-600 dark:hover:border-rose-400/30 dark:hover:bg-rose-400/10 dark:hover:text-rose-300`
			}
		>
			<LogOut size={isDesktop ? 16 : 18} />
			Sign out
		</button>
	</form>
{:else}
	<a
		bind:this={firstActionRef}
		href={loginUrl}
		class={
			isDesktop
				? `${sharedClass} min-h-[3.25rem] text-ink hover:border-stone-300 hover:bg-mist/40 dark:hover:border-slate-600 dark:hover:bg-slate-800`
				: `${sharedClass} min-h-14 text-ink hover:border-stone-300 hover:bg-mist/40 dark:hover:border-slate-600 dark:hover:bg-slate-800`
		}
		onclick={onNavigate}
	>
		<LogIn size={isDesktop ? 16 : 18} />
		Sign in
	</a>
{/if}
