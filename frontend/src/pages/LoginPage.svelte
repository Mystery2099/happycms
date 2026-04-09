<script lang="ts">
	import { User, Lock, LogIn, Eye, EyeOff, AlertCircle } from '@lucide/svelte';

	interface Props {
		loginUrl: string;
		homeUrl: string;
		csrfToken?: string;
		error?: string;
		redirectTo?: string;
		initialEmail?: string;
		rememberMe?: boolean;
	}

	let {
		loginUrl,
		homeUrl,
		csrfToken = '',
		error = '',
		redirectTo = '',
		initialEmail = '',
		rememberMe = false
	}: Props = $props();

	let password = $state('');
	let showPassword = $state(false);
	let isLoading = $state(false);

	function handleSubmit() {
		isLoading = true;
		// Form submits to backend - the isLoading state provides visual feedback
		return true;
	}

	function togglePassword() {
		showPassword = !showPassword;
	}
</script>

<section class="min-h-[calc(100vh-200px)] flex items-center justify-center px-6 py-16">
		<div class="w-full max-w-md">
			<!-- Login Card -->
			<div
				class="border border-mist bg-canvas-elevated p-8 shadow-sm rounded-xl dark:border-slate-700 dark:bg-slate-800/95 dark:shadow-card"
			>
			<!-- Header -->
			<div class="text-center mb-8">
				<div class="inline-flex items-center justify-center w-12 h-12 rounded-full bg-coral/10 mb-4">
					<User size={24} class="text-coral" />
				</div>
				<h1 class="font-display text-2xl text-ink mb-2">Welcome back</h1>
				<p class="text-stone text-sm">Sign in to access your happy thoughts</p>
			</div>

			<!-- Error Message -->
			{#if error}
				<div class="mb-6 p-4 bg-red-50 border border-red-200 rounded-md flex items-start gap-3" role="alert">
					<AlertCircle size={18} class="text-red-500 shrink-0 mt-0.5" />
					<p class="text-sm text-red-700">{error}</p>
				</div>
			{/if}

			<!-- Login Form -->
			<form method="POST" action={loginUrl} class="space-y-6" onsubmit={handleSubmit}>
				<input type="hidden" name="csrf_token" value={csrfToken} />
				<input type="hidden" name="redirect" value={redirectTo} />

				<!-- Email Field -->
				<div class="space-y-2">
					<label for="email" class="block text-sm font-medium text-ink">Email address</label>
					<div class="relative">
						<div class="absolute left-0 top-0 bottom-0 flex items-center pl-3 pointer-events-none">
							<User size={16} class="text-stone" />
						</div>
							<input
								type="email"
							id="email"
							name="email"
							value={initialEmail}
							placeholder="you@example.com"
							required
							autocomplete="email"
								class="w-full pl-10 pr-4 py-3 border-b-2 border-mist bg-transparent text-ink placeholder:text-stone/85 focus:border-coral focus:outline-none transition-colors rounded-md dark:border-slate-600 dark:text-slate-100 dark:placeholder:text-slate-400"
						/>
				</div>
			</div>

			<!-- Password Field -->
				<div class="space-y-2">
					<div class="flex items-center justify-between">
						<label for="password" class="block text-sm font-medium text-ink">Password</label>
						<!-- Backend: Add forgot password link here -->
						<!-- <a href="/forgot-password" class="text-xs text-coral hover:underline">Forgot password?</a> -->
					</div>
					<div class="relative">
						<div class="absolute left-0 top-0 bottom-0 flex items-center pl-3 pointer-events-none">
							<Lock size={16} class="text-stone" />
						</div>
							<input
								type={showPassword ? 'text' : 'password'}
							id="password"
							name="password"
							bind:value={password}
							placeholder="Enter your password"
							required
							autocomplete="current-password"
								class="w-full pl-10 pr-12 py-3 border-b-2 border-mist bg-transparent text-ink placeholder:text-stone/85 focus:border-coral focus:outline-none transition-colors rounded-md dark:border-slate-600 dark:text-slate-100 dark:placeholder:text-slate-400"
						/>
							<button
								type="button"
								onclick={togglePassword}
								class="absolute right-0 top-0 bottom-0 flex items-center pr-3 text-stone hover:text-ink transition-colors dark:text-slate-400 dark:hover:text-slate-100"
								aria-label={showPassword ? 'Hide password' : 'Show password'}
						>
							{#if showPassword}
								<EyeOff size={18} />
							{:else}
								<Eye size={18} />
							{/if}
						</button>
					</div>
				</div>

				<!-- Remember Me -->
				<div class="flex items-center">
						<input
							type="checkbox"
						id="remember"
						name="remember"
						checked={rememberMe}
							class="w-4 h-4 border-mist rounded bg-canvas-elevated text-coral focus:ring-coral focus:ring-offset-0 cursor-pointer dark:border-slate-600 dark:bg-slate-800"
						/>
					<label for="remember" class="ml-2 text-sm text-stone cursor-pointer select-none">
						Remember me for 30 days
					</label>
				</div>

				<!-- Submit Button -->
				<button
					type="submit"
					disabled={isLoading}
					class="w-full inline-flex items-center justify-center gap-2 px-6 py-3 bg-ink text-canvas text-sm font-medium rounded-md transition-all duration-200 hover:bg-coral hover:-translate-y-0.5 hover:shadow-lg hover:shadow-coral/20 active:scale-95 disabled:opacity-50 disabled:cursor-not-allowed disabled:hover:transform-none"
				>
					{#if isLoading}
						<span class="animate-spin inline-block w-4 h-4 border-2 border-current border-t-transparent rounded-full" aria-hidden="true"></span>
						<span>Signing in...</span>
					{:else}
						<LogIn size={16} />
						<span>Sign in</span>
					{/if}
				</button>
			</form>

			<!-- Back to Home -->
			<div class="mt-6 text-center">
				<a href={homeUrl} class="text-sm text-stone hover:text-ink transition-colors inline-flex items-center gap-1">
					← Back to home
				</a>
			</div>
		</div>

		<!-- Sign Up Prompt (optional - remove if not needed) -->
		<div class="mt-6 text-center">
			<p class="text-sm text-stone">
				<!-- Backend: Uncomment and add register URL when ready -->
				<!-- Don't have an account? 
				<a href="/register" class="text-coral hover:underline font-medium">Create one</a> -->
			</p>
		</div>
	</div>
</section>

<style>
	/* Dark mode styles for login form */
	:global(html.dark) .bg-red-50 {
		background-color: rgba(239, 68, 68, 0.1);
	}
	:global(html.dark) .border-red-200 {
		border-color: rgba(239, 68, 68, 0.3);
	}
	:global(html.dark) .text-red-700 {
		color: #fca5a5;
	}
</style>
