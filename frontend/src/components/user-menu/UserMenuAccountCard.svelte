<script lang="ts">
  interface Props {
    isLoggedIn: boolean;
    userName: string;
    userEmail: string;
    variant?: "desktop" | "mobile";
  }

  let {
    isLoggedIn,
    userName,
    userEmail,
    variant = "desktop",
  }: Props = $props();
</script>

<div
  class={[
    "border-stone-200/80 rounded-lg border bg-white/80 px-4 py-4 dark:border-slate-700 dark:bg-slate-800/90",
    variant === "desktop" ? "shadow-sm" : "",
  ]}
>
  <p class="text-sm font-medium text-ink dark:text-slate-100">
    {isLoggedIn ? userName || "User" : "Not signed in"}
  </p>
  <p class="mt-1 text-xs text-stone dark:text-slate-400">
    {isLoggedIn
      ? userEmail || "Manage your preferences"
      : "Sign in to manage your account"}
  </p>
</div>
