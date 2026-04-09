<script lang="ts">
  import { onMount } from "svelte";

  interface Props {
    type: "success" | "error";
    message: string;
  }

  let { type, message }: Props = $props();
  let isMounted = $state(false);

  onMount(() => {
    requestAnimationFrame(() => {
      isMounted = true;
    });
  });
</script>

<div
  class={[
    "rounded-b-lg border-b border-mist bg-canvas-elevated transition-[transform,opacity] duration-250 ease-enter motion-reduce:transition-none",
    isMounted ? "translate-y-0 opacity-100" : "-translate-y-full opacity-0",
  ]}
  role={type === "error" ? "alert" : "status"}
  aria-live={type === "error" ? "assertive" : "polite"}
  aria-atomic="true"
>
  <div class="mx-auto max-w-6xl px-6 py-4 lg:px-8">
    <div
      class={[
        "flex items-start gap-3 text-sm",
        type === "success" ? "text-moss" : "text-coral",
      ]}
    >
      <span
        class={[
          "inline-flex h-5 w-5 items-center justify-center rounded-full text-xs font-semibold leading-none",
          type === "success"
            ? "bg-moss/15 text-moss"
            : "bg-coral/15 text-coral",
        ]}
        aria-hidden="true">{type === "success" ? "✓" : "!"}</span
      >
      <p class="min-w-0 leading-relaxed">{message}</p>
    </div>
  </div>
</div>
