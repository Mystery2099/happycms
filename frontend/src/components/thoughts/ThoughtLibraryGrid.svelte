<script lang="ts">
  import type { ThoughtRecord } from "../../lib/types";
  import ThoughtActions from "./ThoughtActions.svelte";
  import ThoughtCategoryBadge from "./ThoughtCategoryBadge.svelte";
  import ThoughtMood from "./ThoughtMood.svelte";

  interface Props {
    thoughts: ThoughtRecord[];
    gridClass: string;
    bodyClass: string;
    titleClass: string;
    metaClass: string;
    actionRowClass: string;
  }

  let {
    thoughts,
    gridClass,
    bodyClass,
    titleClass,
    metaClass,
    actionRowClass,
  }: Props = $props();

  function normalizeImageUrl(url: string | null): string | null {
    if (!url) return null;
    if (typeof location === "undefined") return url;
    const currentProto = location.protocol === "https:" ? "https:" : "http:";
    return url.replace(/^https?:/, currentProto);
  }
</script>

<div class={`grid ${gridClass}`}>
  {#each thoughts as thought (thought.id)}
    <article
      class="hover:border-coral/25 hover:bg-coral/5 group overflow-hidden rounded-md border border-mist bg-canvas-elevated transition-all duration-200 hover:-translate-y-px hover:shadow-card-hover dark:bg-slate-800/95 dark:hover:border-rose-300/25 dark:hover:bg-slate-700/95"
    >
      {#if thought.imageUrl}
        <div class="aspect-[16/10] overflow-hidden">
          <img
            src={normalizeImageUrl(thought.imageUrl) ?? thought.imageUrl}
            alt={thought.title}
            class="h-full w-full object-cover transition-transform duration-500 group-hover:scale-105"
          />
        </div>
      {/if}
      <div class={bodyClass}>
        <div class="flex items-start justify-between gap-3">
          <h3 class={["font-display leading-tight text-ink", titleClass]}>
            {thought.title}
          </h3>
          <ThoughtCategoryBadge category={thought.category} variant="accent" />
        </div>
        <p
          class="whitespace-normal break-words text-sm leading-relaxed text-stone"
        >
          {thought.thought}
        </p>
        <div
          class={[
            "border-mist/60 flex items-center justify-between border-t",
            metaClass,
          ]}
        >
          <span class="text-sm font-medium text-ink">{thought.author}</span>
          <ThoughtMood score={thought.moodScore} className="text-sm" />
        </div>
        <div class={["flex items-center", actionRowClass]}>
          <ThoughtActions
            editUrl={thought.editUrl}
            deleteUrl={thought.deleteUrl}
          />
        </div>
      </div>
    </article>
  {/each}
</div>
