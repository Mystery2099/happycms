<script lang="ts">
  import type { IconComponent } from "../../lib/types";

  type FeatureItem = {
    number: string;
    title: string;
    description: string;
    icon: IconComponent;
  };

  interface Props {
    items: FeatureItem[];
  }

  let { items }: Props = $props();
</script>

<div class="space-y-4">
  {#each items as item (item.number)}
    <div class="flex gap-4">
      <span class="inline-flex items-center gap-2 font-medium text-coral">
        <item.icon size={16} />
        {item.number}
      </span>
      <div>
        <p class="font-medium text-ink">{item.title}</p>
        <p class="text-sm text-stone">{item.description}</p>
      </div>
    </div>
  {/each}
</div>
