<script lang="ts">
  import type { RecentThought } from "../../lib/types";
  import ThoughtCategoryBadge from "./ThoughtCategoryBadge.svelte";
  import ThoughtMood from "./ThoughtMood.svelte";

  interface Props {
    thoughts: RecentThought[];
  }

  let { thoughts }: Props = $props();
</script>

<div class="border border-mist">
  <div class="md:hidden">
    <div class="divide-y divide-mist">
      {#each thoughts as thought (thought.id)}
        <article class="space-y-4 p-5">
          <div class="flex items-start justify-between gap-3">
            <div class="min-w-0">
              {#if thought.editUrl}
                <a
                  href={thought.editUrl}
                  class="thought-title-link font-display text-xl leading-tight"
                >
                  {thought.title}
                </a>
              {:else}
                <p class="font-display text-xl leading-tight text-ink">
                  {thought.title}
                </p>
              {/if}
              <p class="mt-2 line-clamp-2 text-sm leading-relaxed text-stone">
                {thought.thought}
              </p>
            </div>
            <ThoughtCategoryBadge category={thought.category} />
          </div>

          <div
            class="flex items-center justify-between gap-3 border-t border-mist pt-3"
          >
            <p class="text-sm font-medium text-stone">{thought.author}</p>
            <ThoughtMood score={thought.moodScore} className="text-sm" />
          </div>
        </article>
      {/each}
    </div>
  </div>

  <div class="hidden overflow-x-auto md:block">
    <table class="data-table min-w-[40rem]">
      <caption class="sr-only">Recent happy thoughts from the database</caption>
      <thead>
        <tr>
          <th>Title</th>
          <th>Author</th>
          <th>Category</th>
          <th class="text-right">Mood</th>
        </tr>
      </thead>
      <tbody>
        {#each thoughts as thought (thought.id)}
          <tr>
            <td>
              {#if thought.editUrl}
                <a
                  href={thought.editUrl}
                  class="thought-title-link font-medium"
                >
                  {thought.title}
                </a>
              {:else}
                <p class="font-medium text-ink">{thought.title}</p>
              {/if}
              <p class="mt-1 line-clamp-1 text-sm text-stone">
                {thought.thought}
              </p>
            </td>
            <td class="text-stone">{thought.author}</td>
            <td>
              <ThoughtCategoryBadge category={thought.category} />
            </td>
            <td class="text-right">
              <ThoughtMood score={thought.moodScore} />
            </td>
          </tr>
        {/each}
      </tbody>
    </table>
  </div>
</div>

<style>
  .thought-title-link {
    @apply text-ink underline-offset-4 transition-all;
    text-decoration-color: color-mix(in srgb, var(--coral) 50%, transparent);
  }

  .thought-title-link:hover {
    @apply text-coral underline;
  }
</style>
