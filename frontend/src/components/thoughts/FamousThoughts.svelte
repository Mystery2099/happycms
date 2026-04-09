<script lang="ts">
  import { onMount } from "svelte";
  import { ChevronLeft, ChevronRight } from "@lucide/svelte";

  type Quote = {
    author: string;
    quote: string;
    category: string;
  };

  let { apiUrl = "api/famous-thoughts.php" } = $props<{ apiUrl?: string }>();

  let loading = $state(true);
  let error = $state("");
  let quotes = $state<Quote[]>([]);
  let index = $state(0);
  let visibleIndex = $state(0);
  let isTransitioning = $state(false);

  let currentQuote = $derived(quotes[visibleIndex] ?? null);

  function cycleQuote(direction: number) {
    if (!quotes.length || isTransitioning) return;

    const nextIndex = (index + direction + quotes.length) % quotes.length;
    isTransitioning = true;
    index = nextIndex;

    requestAnimationFrame(() => {
      visibleIndex = nextIndex;
    });

    setTimeout(() => {
      isTransitioning = false;
    }, 300);
  }

  function handleQuoteNavKeydown(event: KeyboardEvent, direction: number) {
    if (event.key === "Enter" || event.key === " ") {
      event.preventDefault();
      cycleQuote(direction);
    }
  }

  onMount(() => {
    fetch(apiUrl)
      .then((response) => {
        if (!response.ok) {
          throw new Error("Unable to load quotes");
        }

        return response.json();
      })
      .then((payload) => {
        quotes = payload.quotes ?? [];
        loading = false;
      })
      .catch((caught) => {
        error =
          caught instanceof Error ? caught.message : "Unable to load quotes";
        loading = false;
      });
  });
</script>

<div class="grid gap-12 lg:grid-cols-2 lg:gap-16">
  <div>
    <h2 class="mb-4 font-display text-2xl text-ink sm:text-3xl lg:text-4xl">
      Interactive Display
    </h2>
    <p class="mb-8 text-stone">
      Three client-driven formatting changes demonstrate JavaScript
      interactivity:
    </p>

    <div class="space-y-6">
      <div class="group flex items-start gap-4">
        <span
          class="flex h-8 w-8 flex-shrink-0 items-center justify-center bg-coral text-sm font-medium text-white transition-transform duration-200 group-hover:scale-110"
          >1</span
        >
        <div
          class="transition-transform duration-200 group-hover:translate-x-1"
        >
          <p class="font-medium text-ink">View Modes</p>
          <p class="text-sm text-stone">
            Toggle between table and grid layouts
          </p>
        </div>
      </div>

      <div class="group flex items-start gap-4">
        <span
          class="flex h-8 w-8 flex-shrink-0 items-center justify-center bg-coral text-sm font-medium text-white transition-transform duration-200 group-hover:scale-110"
          >2</span
        >
        <div
          class="transition-transform duration-200 group-hover:translate-x-1"
        >
          <p class="font-medium text-ink">Density Control</p>
          <p class="text-sm text-stone">
            Adjust spacing for compact or comfortable viewing
          </p>
        </div>
      </div>

      <div class="group flex items-start gap-4">
        <span
          class="flex h-8 w-8 flex-shrink-0 items-center justify-center bg-coral text-sm font-medium text-white transition-transform duration-200 group-hover:scale-110"
          >3</span
        >
        <div
          class="transition-transform duration-200 group-hover:translate-x-1"
        >
          <p class="font-medium text-ink">Theme Switching</p>
          <p class="text-sm text-stone">
            Toggle light, dark, or system-based visual styling
          </p>
        </div>
      </div>
    </div>
  </div>

  <div class="lg:border-l lg:border-mist lg:pl-12">
    <div
      class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between"
    >
      <div>
        <p class="text-sm font-medium uppercase tracking-wider text-stone">
          Fetch API
        </p>
        <h3 class="font-display text-2xl text-ink">Famous Happy Thoughts</h3>
      </div>
      {#if !loading && !error}
        <span class="text-sm text-stone">{index + 1} / {quotes.length}</span>
      {/if}
    </div>

    {#if loading}
      <div class="py-12 text-center" aria-live="polite">
        <div class="relative inline-block">
          <div
            class="h-8 w-8 animate-spin rounded-full border-2 border-mist border-t-coral"
          ></div>
          <div
            class="border-mist/30 border-t-coral/50 absolute inset-0 h-8 w-8 animate-ping rounded-full border-2 opacity-20"
          ></div>
        </div>
        <p class="mt-4 animate-pulse text-sm text-stone">Loading quotes...</p>
      </div>
    {:else if error}
      <div class="bg-coral/10 border-coral/20 border px-6 py-8" role="alert">
        <p class="text-sm text-coral">{error}</p>
      </div>
    {:else if currentQuote}
      <div class="relative" aria-live="polite" aria-atomic="true">
        <p class="sr-only">
          Showing quote {visibleIndex + 1} of {quotes.length}
        </p>
        <span
          class="quote-mark absolute -left-2 -top-4 transition-transform duration-500 hover:scale-110"
          aria-hidden="true">"</span
        >
        <blockquote
          class={[
            "pl-6 font-display text-2xl leading-relaxed text-ink transition-[opacity,filter] duration-250 ease-enter motion-reduce:transition-none lg:text-3xl",
            isTransitioning ? "opacity-0 blur-[2px]" : "opacity-100 blur-0",
          ]}
        >
          {currentQuote.quote}
        </blockquote>

        <div
          class={[
            "mt-8 flex flex-col gap-4 transition-opacity duration-250 ease-out motion-reduce:transition-none sm:flex-row sm:items-center sm:justify-between",
            isTransitioning ? "opacity-0" : "opacity-100",
          ]}
        >
          <div>
            <p class="font-medium text-ink">{currentQuote.author}</p>
            <p class="text-sm text-stone">{currentQuote.category}</p>
          </div>

          <div class="flex gap-2">
            <button
              type="button"
              class="flex h-10 w-10 items-center justify-center border border-mist transition-[transform,background-color,border-color] duration-200 ease-enter hover:-translate-x-0.5 hover:border-coral active:scale-[0.97] motion-reduce:transition-none dark:border-slate-600 dark:text-slate-300 dark:hover:border-coral dark:hover:text-coral"
              onclick={() => cycleQuote(-1)}
              onkeydown={(e) => handleQuoteNavKeydown(e, -1)}
              aria-label="Previous quote"
            >
              <ChevronLeft size={16} />
            </button>
            <button
              type="button"
              class="flex h-10 w-10 items-center justify-center bg-ink text-canvas transition-[transform,background-color] duration-200 ease-enter hover:translate-x-0.5 hover:bg-coral active:scale-[0.97] motion-reduce:transition-none dark:bg-slate-700 dark:text-slate-100 dark:hover:bg-coral"
              onclick={() => cycleQuote(1)}
              onkeydown={(e) => handleQuoteNavKeydown(e, 1)}
              aria-label="Next quote"
            >
              <ChevronRight size={16} />
            </button>
          </div>
        </div>
      </div>
    {/if}
  </div>
</div>
