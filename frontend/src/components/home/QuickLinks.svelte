<script lang="ts">
  import { ArrowRight } from "@lucide/svelte";
  import type { IconComponent } from "../../lib/types";

  type QuickLink = {
    href: string | null;
    label: string;
    icon: IconComponent;
  };

  interface Props {
    items: QuickLink[];
  }

  let { items }: Props = $props();
</script>

<nav class="space-y-3" aria-label="Primary actions">
  {#each items as item (item.label)}
    {#if item.href}
      <a
        href={item.href}
        class="hover:bg-coral/5 group flex items-center justify-between rounded-md border-b border-mist px-3 py-3 transition-all duration-200 hover:-translate-y-px"
      >
        <span
          class="inline-flex min-w-0 items-center gap-3 text-ink transition-colors group-hover:text-coral"
        >
          <item.icon size={16} class="shrink-0" />
          <span class="truncate">{item.label}</span>
        </span>
        <ArrowRight
          size={16}
          class="text-stone transition-all duration-200 group-hover:translate-x-1 group-hover:text-coral"
        />
      </a>
    {/if}
  {/each}
</nav>
