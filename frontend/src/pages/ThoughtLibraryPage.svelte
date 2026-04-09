<script lang="ts">
  import { innerWidth } from "svelte/reactivity/window";
  import { PlusCircle, Search } from "@lucide/svelte";
  import Dropdown from "../components/Dropdown.svelte";
  import DisplayHeading from "../components/site/DisplayHeading.svelte";
  import EmptyState from "../components/site/EmptyState.svelte";
  import Section from "../components/site/Section.svelte";
  import SegmentedControl from "../components/site/SegmentedControl.svelte";
  import ThoughtLibraryGrid from "../components/thoughts/ThoughtLibraryGrid.svelte";
  import ThoughtLibraryTable from "../components/thoughts/ThoughtLibraryTable.svelte";
  import type { ThoughtRecord } from "../lib/types";

  type Routes = {
    create: string | null;
    search: string;
  };

  interface Props {
    pageMode: "library" | "search";
    heading: string;
    description: string;
    emptyMessage: string;
    serverSearch: string;
    searchAction: string;
    thoughts: ThoughtRecord[];
    categories: string[];
    routes: Routes;
  }

  let {
    pageMode,
    heading,
    description,
    emptyMessage,
    serverSearch,
    searchAction,
    thoughts,
    categories,
    routes,
  }: Props = $props();

  let search = $state("");
  let category = $state("all");
  let view = $state<"grid" | "table">("table");
  let density = $state<"compact" | "comfortable">("comfortable");

  let viewportWidth = $derived(innerWidth.current ?? 1024);
  let isMobile = $derived(viewportWidth < 768);
  let visibleThoughts = $derived.by(() => {
    const normalizedSearch = search.trim().toLowerCase();
    const normalizedCategory = category.toLowerCase();

    return thoughts.filter((thought) => {
      const haystack = [
        thought.title,
        thought.author,
        thought.category,
        thought.thought,
      ]
        .join(" ")
        .toLowerCase();

      const matchesSearch =
        normalizedSearch === "" || haystack.includes(normalizedSearch);
      const matchesCategory =
        normalizedCategory === "all" ||
        thought.category.toLowerCase() === normalizedCategory;

      return matchesSearch && matchesCategory;
    });
  });
  let visibleCount = $derived(visibleThoughts.length);
  let totalCount = $derived(thoughts.length);
  let categoryOptions = $derived([
    { value: "all", label: "All categories" },
    ...categories.map((item) => ({ value: item.toLowerCase(), label: item })),
  ]);
  let cardGridClass = $derived(
    density === "compact"
      ? "grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4"
      : "grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 md:gap-6",
  );
  let tableCellClass = $derived(density === "compact" ? "py-2.5" : "py-5");
  let tableCellPadding = $derived(
    density === "compact" ? "0.625rem" : "1.25rem",
  );
  let cardBodyClass = $derived(
    density === "compact" ? "space-y-2 p-4" : "space-y-4 p-5 md:p-6",
  );
  let cardTitleClass = $derived(
    density === "compact" ? "text-lg md:text-lg" : "text-lg md:text-xl",
  );
  let cardMetaClass = $derived(density === "compact" ? "pt-2" : "pt-3");
  let cardActionRowClass = $derived(
    density === "compact" ? "gap-3 pt-1" : "gap-4 pt-2",
  );

  $effect(() => {
    if (search === "" && serverSearch !== "") {
      search = serverSearch;
    }
  });
</script>

<Section>
  <div class="space-y-6 md:space-y-8">
    {#if pageMode === "search"}
      <div class="mb-12 max-w-xl">
        <DisplayHeading
          level="h1"
          eyebrow="Search"
          title="Find happy thoughts"
          icon={Search}
          className="mb-6"
        />

        <form
          action={searchAction}
          method="get"
          class="grid gap-3 sm:grid-cols-[minmax(0,1fr)_auto] sm:items-end"
          role="search"
        >
          <label for="search-query" class="sr-only">Search thoughts</label>
          <input
            id="search-query"
            type="search"
            name="q"
            bind:value={search}
            placeholder="Search by title, author, category, or content..."
            class="input-minimal min-w-0"
            aria-label="Search thoughts"
          />
          <button
            type="submit"
            class="btn-primary justify-center whitespace-nowrap"
          >
            <Search size={16} />
            Search
          </button>
        </form>
      </div>
    {/if}

    {#if pageMode !== "search"}
      <div
        class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between"
      >
        <div>
          <DisplayHeading
            level="h1"
            title={heading}
            {description}
            className="space-y-1 md:space-y-2"
            descriptionClass="hidden text-sm md:text-base sm:block"
          />
        </div>

        <div class="flex flex-wrap gap-3">
          {#if routes.create}
            <a
              href={routes.create}
              class="btn-primary py-2 text-sm md:py-3 md:text-base"
            >
              <PlusCircle size={16} />
              Add thought
            </a>
          {/if}
          <a
            href={routes.search}
            class="btn-secondary py-2 text-sm md:py-3 md:text-base"
          >
            <Search size={16} />
            Search thoughts
          </a>
        </div>
      </div>
    {/if}

    <div
      class="flex flex-col gap-4 border-y border-mist py-4 sm:flex-row sm:items-center sm:justify-between"
    >
      <div class="flex flex-wrap items-center gap-3">
        <span
          class="text-sm tabular-nums text-stone transition-all duration-300"
          role="status"
          aria-live="polite"
          aria-atomic="true"
        >
          <span
            class:opacity-50={visibleCount === 0}
            class="inline-block transition-all duration-300"
            >{visibleCount}</span
          >
          <span class="text-mist">/</span>
          <span class="inline-block">{totalCount}</span>
          <span class="ml-1">shown</span>
        </span>

        {#if !isMobile}
          <div class="flex items-center gap-1 border-l border-mist pl-3">
            <SegmentedControl
              options={[
                { value: "table", label: "Table", title: "Table view" },
                { value: "grid", label: "Grid", title: "Grid view" },
              ]}
              bind:value={view}
              ariaLabel="View mode"
              buttonClass="px-2.5 py-1.5"
            />
          </div>

          <div class="flex items-center gap-1 border-l border-mist pl-3">
            <SegmentedControl
              options={[
                { value: "compact", label: "Compact", title: "Compact view" },
                {
                  value: "comfortable",
                  label: "Comfortable",
                  title: "Comfortable view",
                },
              ]}
              bind:value={density}
              ariaLabel="Density"
            />
          </div>
        {/if}
      </div>

      <div class="flex flex-col gap-3 sm:flex-row sm:items-center">
        <div class="min-w-[220px]">
          <span id="client-filter-label" class="sr-only"
            >Filter by category</span
          >
          <Dropdown
            options={categoryOptions}
            bind:value={category}
            ariaLabel="Filter by category"
            ariaLabelledby="client-filter-label"
            listAriaLabel="Category filters"
          />
        </div>
        <input
          type="search"
          bind:value={search}
          class="input-minimal min-w-[220px]"
          placeholder="Filter visible thoughts"
          aria-label="Filter visible thoughts"
        />
      </div>
    </div>

    {#if thoughts.length === 0}
      <EmptyState
        title={emptyMessage}
        description="Start collecting happy moments"
      >
        {#if routes.create}
          <a href={routes.create} class="btn-primary">
            <PlusCircle size={16} />
            Add Your First Thought
          </a>
        {/if}
      </EmptyState>
    {:else if visibleThoughts.length === 0}
      <EmptyState
        title="No thoughts match the current filters."
        description="Adjust the search text or category filter to widen the results."
      />
    {:else if !isMobile && view === "table"}
      <ThoughtLibraryTable
        thoughts={visibleThoughts}
        rowClass={tableCellClass}
        rowPadding={tableCellPadding}
      />
    {:else}
      <ThoughtLibraryGrid
        thoughts={visibleThoughts}
        gridClass={cardGridClass}
        bodyClass={cardBodyClass}
        titleClass={cardTitleClass}
        metaClass={cardMetaClass}
        actionRowClass={cardActionRowClass}
      />
    {/if}
  </div>
</Section>
