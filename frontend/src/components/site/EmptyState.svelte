<script lang="ts">
  import type { Snippet } from "svelte";

  interface Props {
    title: string;
    description?: string;
    children?: Snippet;
    className?: string;
  }

  let { title, description = "", children, className = "" }: Props = $props();
</script>

<div
  class={[
    "rounded-lg border border-dashed border-mist px-4 py-12 text-center sm:px-6 sm:py-16",
    className,
  ]}
>
  <p class="mb-2 font-display text-xl text-ink">{title}</p>
  {#if description}
    <p class="mb-6 text-stone">{description}</p>
  {/if}
  {#if children}
    {@render children()}
  {/if}
</div>
