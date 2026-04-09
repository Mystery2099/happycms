<script lang="ts">
  import { User, Lock, LogIn, Eye, EyeOff, AlertCircle } from "@lucide/svelte";
  import LoginField from "../components/login/LoginField.svelte";

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
    csrfToken = "",
    error = "",
    redirectTo = "",
    initialEmail = "",
    rememberMe = false,
  }: Props = $props();

  let password = $state("");
  let showPassword = $state(false);
  let isLoading = $state(false);

  function handleSubmit() {
    isLoading = true;
    return true;
  }

  function togglePassword() {
    showPassword = !showPassword;
  }
</script>

<section
  class="flex min-h-[calc(100vh-200px)] items-center justify-center px-6 py-16"
>
  <div class="w-full max-w-md">
    <div
      class="rounded-xl border border-mist bg-canvas-elevated p-8 shadow-sm dark:border-slate-700 dark:bg-slate-800/95 dark:shadow-card"
    >
      <div class="mb-8 text-center">
        <div
          class="bg-coral/10 mb-4 inline-flex h-12 w-12 items-center justify-center rounded-full"
        >
          <User size={24} class="text-coral" />
        </div>
        <h1 class="mb-2 font-display text-2xl text-ink sm:text-3xl">
          Welcome back
        </h1>
        <p class="text-sm text-stone">Sign in to access your happy thoughts</p>
      </div>

      {#if error}
        <div
          class="mb-6 flex items-start gap-3 rounded-md border border-red-200 bg-red-50 p-4"
          role="alert"
        >
          <AlertCircle size={18} class="mt-0.5 shrink-0 text-red-500" />
          <p class="text-sm text-red-700">{error}</p>
        </div>
      {/if}

      <form
        method="POST"
        action={loginUrl}
        class="space-y-6"
        onsubmit={handleSubmit}
      >
        <input type="hidden" name="csrf_token" value={csrfToken} />
        <input type="hidden" name="redirect" value={redirectTo} />

        <LoginField
          id="email"
          name="email"
          label="Email address"
          icon={User}
          type="email"
          value={initialEmail}
          placeholder="you@example.com"
          required
          autocomplete="email"
        />

        <LoginField
          id="password"
          name="password"
          label="Password"
          icon={Lock}
          type={showPassword ? "text" : "password"}
          bind:value={password}
          placeholder="Enter your password"
          required
          autocomplete="current-password"
        >
          {#snippet trailingControl()}
            <button
              type="button"
              onclick={togglePassword}
              class="text-stone transition-colors hover:text-ink dark:text-slate-400 dark:hover:text-slate-100"
              aria-label={showPassword ? "Hide password" : "Show password"}
            >
              {#if showPassword}
                <EyeOff size={18} />
              {:else}
                <Eye size={18} />
              {/if}
            </button>
          {/snippet}
        </LoginField>

        <div class="flex items-center">
          <input
            type="checkbox"
            id="remember"
            name="remember"
            checked={rememberMe}
            class="h-4 w-4 cursor-pointer rounded border-mist bg-canvas-elevated text-coral focus:ring-coral focus:ring-offset-0 dark:border-slate-600 dark:bg-slate-800"
          />
          <label
            for="remember"
            class="ml-2 cursor-pointer select-none text-sm text-stone"
          >
            Remember me for 30 days
          </label>
        </div>

        <button
          type="submit"
          disabled={isLoading}
          class="hover:shadow-coral/20 inline-flex w-full items-center justify-center gap-2 rounded-md bg-ink px-6 py-3 text-sm font-medium text-canvas transition-all duration-200 hover:-translate-y-0.5 hover:bg-coral hover:shadow-lg active:scale-95 disabled:cursor-not-allowed disabled:opacity-50 disabled:hover:transform-none"
        >
          {#if isLoading}
            <span
              class="inline-block h-4 w-4 animate-spin rounded-full border-2 border-current border-t-transparent"
              aria-hidden="true"
            ></span>
            <span>Signing in...</span>
          {:else}
            <LogIn size={16} />
            <span>Sign in</span>
          {/if}
        </button>
      </form>

      <div class="mt-6 text-center">
        <a
          href={homeUrl}
          class="inline-flex items-center gap-1 text-sm text-stone transition-colors hover:text-ink"
        >
          ← Back to home
        </a>
      </div>
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
