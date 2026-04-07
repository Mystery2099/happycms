<script lang="ts">
	import { User, LogOut, ChevronDown } from '@lucide/svelte';

	interface Props {
		isLoggedIn: boolean;
		userName?: string;
		userEmail?: string;
		loginUrl: string;
		logoutUrl: string;
		logoutCsrfToken?: string;
	}

	let {
		isLoggedIn,
		userName = '',
		userEmail = '',
		loginUrl,
		logoutUrl,
		logoutCsrfToken = ''
	}: Props = $props();

	let isOpen = $state(false);
	let dropdownRef = $state<HTMLDivElement | null>(null);

	function toggleMenu() {
		isOpen = !isOpen;
	}

	function handleDocumentKeydown(event: KeyboardEvent) {
		if (event.key === 'Escape') {
			isOpen = false;
		}
	}

	// Close dropdown when clicking outside
	$effect(() => {
		function handleClickOutside(event: MouseEvent) {
			if (dropdownRef && !dropdownRef.contains(event.target as Node)) {
				isOpen = false;
			}
		}

		if (isOpen) {
			document.addEventListener('click', handleClickOutside);
			return () => document.removeEventListener('click', handleClickOutside);
		}
	});
</script>

<svelte:document onkeydown={handleDocumentKeydown} />

{#if isLoggedIn}
	<!-- User Menu (Logged In) -->
	<div class="relative" bind:this={dropdownRef}>
		<button
			type="button"
			onclick={toggleMenu}
			class="flex items-center gap-2 px-3 py-2 text-sm font-medium text-ink hover:text-coral transition-colors"
			aria-expanded={isOpen}
			aria-haspopup="menu"
			aria-label="Open user menu"
		>
			<div class="w-8 h-8 rounded-full bg-coral/10 flex items-center justify-center">
				<User size={16} class="text-coral" />
			</div>
			<span class="hidden lg:inline max-w-[120px] truncate">{userName || 'User'}</span>
			<ChevronDown 
				size={14} 
				class="transition-transform duration-200 {isOpen ? 'rotate-180' : ''}" 
			/>
		</button>

		{#if isOpen}
			<div
				class="absolute right-0 mt-2 w-56 bg-white border border-mist shadow-lg z-50 animate-in fade-in slide-in-from-top-1 duration-150"
				role="menu"
				aria-label="User menu"
			>
				<!-- User Info -->
				<div class="px-4 py-3 border-b border-mist/50">
					<p class="text-sm font-medium text-ink truncate">{userName || 'User'}</p>
					<p class="text-xs text-stone truncate">{userEmail || ''}</p>
				</div>

				<!-- Menu Items -->
				<div class="py-1">
					<!-- Backend: Add profile link when ready -->
					<!-- 
					<a 
						href={profileUrl} 
						class="flex items-center gap-3 px-4 py-2 text-sm text-stone hover:text-ink hover:bg-mist/20 transition-colors"
						role="menuitem"
						onclick={closeMenu}
					>
						<User size={16} />
						Profile
					</a>
					-->
					
					<!-- Backend: Add settings link when ready -->
					<!-- 
					<a 
						href={settingsUrl} 
						class="flex items-center gap-3 px-4 py-2 text-sm text-stone hover:text-ink hover:bg-mist/20 transition-colors"
						role="menuitem"
						onclick={closeMenu}
					>
						<Settings size={16} />
						Settings
					</a>
					-->

					<div class="border-t border-mist/50 my-1"></div>

					<form method="POST" action={logoutUrl}>
						<input type="hidden" name="csrf_token" value={logoutCsrfToken} />
						<button
							type="submit"
							class="w-full flex items-center gap-3 px-4 py-2 text-sm text-stone hover:text-red-600 hover:bg-red-50 transition-colors text-left"
							role="menuitem"
						>
							<LogOut size={16} />
							Sign out
						</button>
					</form>
				</div>
			</div>
		{/if}
	</div>
{:else}
	<!-- Login Button (Logged Out) -->
	<a
		href={loginUrl}
		class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-ink border border-mist hover:border-coral hover:text-coral transition-all duration-200"
	>
		<User size={16} />
		<span class="hidden sm:inline">Sign in</span>
	</a>
{/if}

<style>
	@keyframes animate-in {
		from {
			opacity: 0;
			transform: translateY(-4px);
		}
		to {
			opacity: 1;
			transform: translateY(0);
		}
	}

	.animate-in {
		animation: animate-in 0.15s ease-out;
	}

	:global(html.dark) .hover\:bg-red-50:hover {
		background-color: rgba(239, 68, 68, 0.1);
	}
</style>
