<script lang="ts">
	import {
		Image,
		Pencil,
		PlusCircle,
		Search,
		Trash2
	} from '@lucide/svelte';

	type Thought = {
		id: number;
		title: string;
		author: string;
		category: string;
		moodScore: number;
		thought: string;
		imageUrl: string | null;
		editUrl: string;
		deleteUrl: string;
	};

	type Routes = {
		create: string;
		search: string;
	};

	interface Props {
		pageMode: 'library' | 'search';
		heading: string;
		description: string;
		emptyMessage: string;
		serverSearch: string;
		searchAction: string;
		thoughts: Thought[];
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
		routes
	}: Props = $props();

	let search = $state('');
	let category = $state('all');
	let view = $state<'grid' | 'table'>('table');
	let density = $state<'compact' | 'comfortable'>('comfortable');
	let viewportWidth = $state(1024);

	let isMobile = $derived(viewportWidth < 768);
	let visibleThoughts = $derived.by(() => {
		const normalizedSearch = search.trim().toLowerCase();
		const normalizedCategory = category.toLowerCase();

		return thoughts.filter((thought) => {
			const haystack = [
				thought.title,
				thought.author,
				thought.category,
				thought.thought
			]
				.join(' ')
				.toLowerCase();

			const matchesSearch = normalizedSearch === '' || haystack.includes(normalizedSearch);
			const matchesCategory =
				normalizedCategory === 'all' || thought.category.toLowerCase() === normalizedCategory;

			return matchesSearch && matchesCategory;
		});
	});
	let visibleCount = $derived(visibleThoughts.length);
	let totalCount = $derived(thoughts.length);
	let cardGridClass = $derived(
		density === 'compact'
			? 'grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4'
			: 'grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 md:gap-6'
	);
	let tableCellClass = $derived(density === 'compact' ? 'py-2.5' : 'py-5');
	let tableCellPadding = $derived(density === 'compact' ? '0.625rem' : '1.25rem');
	let cardBodyClass = $derived(
		density === 'compact' ? 'space-y-2 p-4' : 'space-y-4 p-5 md:p-6'
	);
	let cardTitleClass = $derived(
		density === 'compact' ? 'text-lg md:text-lg' : 'text-lg md:text-xl'
	);
	let cardMetaClass = $derived(density === 'compact' ? 'pt-2' : 'pt-3');
	let cardActionRowClass = $derived(density === 'compact' ? 'gap-3 pt-1' : 'gap-4 pt-2');

	function updateViewportWidth() {
		viewportWidth = window.innerWidth;
		if (viewportWidth < 768) {
			view = 'grid';
		}
	}

	function renderMood(score: number): string {
		return '★'.repeat(score);
	}

	$effect(() => {
		if (search === '' && serverSearch !== '') {
			search = serverSearch;
		}
	});

	$effect(() => {
		updateViewportWidth();
		window.addEventListener('resize', updateViewportWidth);

		return () => {
			window.removeEventListener('resize', updateViewportWidth);
		};
	});
</script>

<section class="section-padding">
	<div class="max-w-6xl mx-auto px-6 lg:px-8">
		<div class="space-y-6 md:space-y-8">
			{#if pageMode === 'search'}
				<div class="max-w-2xl mb-12">
					<p class="text-sm font-medium text-stone uppercase tracking-widest mb-4">Search</p>
					<h1 class="font-display text-display-md text-ink mb-6 inline-flex items-center gap-3">
						<Search size={24} class="text-coral" />
						Find happy thoughts
					</h1>

					<form action={searchAction} method="get" class="flex gap-3" role="search">
						<label for="search-query" class="sr-only">Search thoughts</label>
						<input
							id="search-query"
							type="search"
							name="q"
							bind:value={search}
							placeholder="Search by title, author, category, or content..."
							class="input-minimal flex-1"
							aria-label="Search thoughts"
						/>
						<button type="submit" class="btn-primary">
							<Search size={16} />
							Search
						</button>
					</form>
				</div>
			{/if}

			<div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
				<div>
					{#if pageMode === 'search'}
						<h2 class="font-display text-display-md text-ink mb-1 md:mb-2">{heading}</h2>
					{:else}
						<h1 class="font-display text-display-md text-ink mb-1 md:mb-2">{heading}</h1>
					{/if}
					<p class="text-stone text-sm md:text-base hidden sm:block">{description}</p>
				</div>

				{#if pageMode !== 'search'}
					<a href={routes.search} class="btn-secondary text-sm py-2 md:py-3 md:text-base">
						<Search size={16} />
						Search thoughts
					</a>
				{/if}
			</div>

			<div
				class="border-mist flex flex-col gap-4 border-y py-4 sm:flex-row sm:items-center sm:justify-between"
			>
				<div class="flex flex-wrap items-center gap-3">
					<span
						class="text-stone text-sm tabular-nums transition-all duration-300"
						role="status"
						aria-live="polite"
						aria-atomic="true"
					>
						<span class:opacity-50={visibleCount === 0} class="inline-block transition-all duration-300"
							>{visibleCount}</span
						>
						<span class="text-mist">/</span>
						<span class="inline-block">{totalCount}</span>
						<span class="ml-1">shown</span>
					</span>

					<div class="border-mist flex items-center gap-1 border-l pl-3">
						<div class="bg-mist/50 flex items-center rounded-lg p-0.5 dark:bg-slate-800/50">
							{#if !isMobile}
								<button
									type="button"
									class="rounded-md px-2.5 py-1.5 text-sm font-medium transition-all duration-200 active:scale-95"
									class:bg-white={view === 'table'}
									class:dark:bg-slate-700={view === 'table'}
									class:shadow-sm={view === 'table'}
									class:ring-2={view === 'table'}
									class:ring-coral={view === 'table'}
									class:ring-offset-1={view === 'table'}
									class:dark:ring-offset-slate-800={view === 'table'}
									class:text-ink={view === 'table'}
									class:text-stone={view !== 'table'}
									class:hover:text-ink={view !== 'table'}
									onclick={() => (view = 'table')}
									aria-pressed={view === 'table'}
									title="Table view"
								>
									Table
								</button>
							{/if}
							<button
								type="button"
								class="rounded-md px-2.5 py-1.5 text-sm font-medium transition-all duration-200 active:scale-95"
								class:bg-white={view === 'grid'}
								class:dark:bg-slate-700={view === 'grid'}
								class:shadow-sm={view === 'grid'}
								class:ring-2={view === 'grid'}
								class:ring-coral={view === 'grid'}
								class:ring-offset-1={view === 'grid'}
								class:dark:ring-offset-slate-800={view === 'grid'}
								class:text-ink={view === 'grid'}
								class:text-stone={view !== 'grid'}
								class:hover:text-ink={view !== 'grid'}
								onclick={() => (view = 'grid')}
								aria-pressed={view === 'grid'}
								title="Grid view"
							>
								Grid
							</button>
						</div>
					</div>

					<div class="border-mist flex items-center gap-1 border-l pl-3">
						<div class="bg-mist/50 flex items-center rounded-lg p-0.5 dark:bg-slate-800/50">
							<button
								type="button"
								class="rounded-md px-3 py-1.5 text-sm font-medium transition-all duration-200 active:scale-95"
								class:bg-white={density === 'compact'}
								class:dark:bg-slate-700={density === 'compact'}
								class:shadow-sm={density === 'compact'}
								class:ring-2={density === 'compact'}
								class:ring-coral={density === 'compact'}
								class:ring-offset-1={density === 'compact'}
								class:dark:ring-offset-slate-800={density === 'compact'}
								class:text-ink={density === 'compact'}
								class:text-stone={density !== 'compact'}
								class:hover:text-ink={density !== 'compact'}
								onclick={() => (density = 'compact')}
								aria-pressed={density === 'compact'}
								title="Compact view"
							>
								Compact
							</button>
							<button
								type="button"
								class="rounded-md px-3 py-1.5 text-sm font-medium transition-all duration-200 active:scale-95"
								class:bg-white={density === 'comfortable'}
								class:dark:bg-slate-700={density === 'comfortable'}
								class:shadow-sm={density === 'comfortable'}
								class:ring-2={density === 'comfortable'}
								class:ring-coral={density === 'comfortable'}
								class:ring-offset-1={density === 'comfortable'}
								class:dark:ring-offset-slate-800={density === 'comfortable'}
								class:text-ink={density === 'comfortable'}
								class:text-stone={density !== 'comfortable'}
								class:hover:text-ink={density !== 'comfortable'}
								onclick={() => (density = 'comfortable')}
								aria-pressed={density === 'comfortable'}
								title="Comfortable view"
							>
								Comfortable
							</button>
						</div>
					</div>
				</div>

				<div class="flex flex-col gap-3 sm:flex-row sm:items-center">
					<div class="min-w-[220px]">
						<label class="sr-only" for="client-filter">Filter by category</label>
						<select id="client-filter" bind:value={category} class="input-minimal">
							<option value="all">All categories</option>
							{#each categories as item (item)}
								<option value={item.toLowerCase()}>{item}</option>
							{/each}
						</select>
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
				<div class="border-mist border border-dashed py-16 text-center">
					<p class="font-display text-xl text-ink mb-2">{emptyMessage}</p>
					<p class="text-stone mb-6">Start collecting happy moments</p>
					<a href={routes.create} class="btn-primary">
						<PlusCircle size={16} />
						Add Your First Thought
					</a>
				</div>
			{:else if visibleThoughts.length === 0}
				<div class="border-mist border border-dashed py-16 text-center">
					<p class="font-display text-xl text-ink mb-2">No thoughts match the current filters.</p>
					<p class="text-stone mb-6">Adjust the search text or category filter to widen the results.</p>
				</div>
			{:else}
				{#if !isMobile && view === 'table'}
					<div class="border-mist overflow-hidden border">
						<table class="data-table">
							<thead>
								<tr>
									<th>Thought</th>
									<th>Author</th>
									<th>Category</th>
									<th>Mood</th>
									<th class="text-right">Actions</th>
								</tr>
							</thead>
							<tbody>
								{#each visibleThoughts as thought (thought.id)}
									<tr>
										<td class="max-w-md {tableCellClass}" style:padding-block={tableCellPadding}>
											<p class="font-medium text-ink">{thought.title}</p>
											<p class="mt-1 text-sm text-stone whitespace-normal break-words">
												{thought.thought}
											</p>
											{#if thought.imageUrl}
												<span class="mt-2 inline-flex items-center gap-1 text-xs text-stone">
													<Image size={12} />
													Has image
												</span>
											{/if}
										</td>
										<td class="text-stone {tableCellClass}" style:padding-block={tableCellPadding}>
											{thought.author}
										</td>
										<td class={tableCellClass} style:padding-block={tableCellPadding}>
											<span
												class="bg-mist/50 text-stone inline-flex items-center px-2.5 py-0.5 text-xs font-medium"
											>
												{thought.category}
											</span>
										</td>
										<td class="text-wheat {tableCellClass}" style:padding-block={tableCellPadding}>
											{renderMood(thought.moodScore)}
										</td>
										<td class="text-right {tableCellClass}" style:padding-block={tableCellPadding}>
											<div class="flex items-center justify-end gap-2">
													<a
														href={thought.editUrl}
														class="text-sm text-stone inline-flex min-h-11 items-center gap-1.5 px-2 py-2 transition-colors hover:text-ink"
													>
														<Pencil size={14} />
														Edit
												</a>
												<span class="text-mist">|</span>
													<a
														href={thought.deleteUrl}
														class="text-coral text-sm inline-flex min-h-11 items-center gap-1.5 px-2 py-2 transition-colors hover:text-ink"
													>
														<Trash2 size={14} />
														Delete
												</a>
											</div>
										</td>
									</tr>
								{/each}
							</tbody>
						</table>
					</div>
				{:else}
					<div class={`grid ${cardGridClass}`}>
						{#each visibleThoughts as thought (thought.id)}
							<article
								class="group bg-white rounded-xl border border-mist overflow-hidden transition-all duration-200 hover:shadow-md"
							>
								{#if thought.imageUrl}
									<div class="aspect-[16/10] overflow-hidden">
										<img
											src={thought.imageUrl}
											alt={thought.title}
											class="h-full w-full object-cover transition-transform duration-500 group-hover:scale-105"
										/>
									</div>
								{/if}
									<div class={cardBodyClass}>
										<div class="flex items-start justify-between gap-3">
											<h3 class={['font-display text-ink leading-tight', cardTitleClass]}>
												{thought.title}
											</h3>
											<span
												class="bg-coral/10 text-coral inline-flex shrink-0 items-center rounded-full px-2.5 py-1 text-xs font-medium"
											>
											{thought.category}
										</span>
									</div>
									<p class="text-sm text-stone leading-relaxed whitespace-normal break-words">
										{thought.thought}
									</p>
										<div class={['border-mist/60 flex items-center justify-between border-t', cardMetaClass]}>
											<span class="text-sm font-medium text-ink">{thought.author}</span>
											<span class="text-wheat text-sm">{renderMood(thought.moodScore)}</span>
										</div>
										<div class={['flex items-center', cardActionRowClass]}>
												<a
													href={thought.editUrl}
													class="text-stone flex min-h-11 items-center gap-1.5 px-2 py-2 text-sm font-medium transition-colors hover:text-ink"
											>
												<Pencil size={14} />
												Edit
											</a>
											<a
												href={thought.deleteUrl}
												class="text-coral flex min-h-11 items-center gap-1.5 px-2 py-2 text-sm font-medium transition-colors hover:text-ink"
											>
													<Trash2 size={14} />
													Delete
											</a>
										</div>
								</div>
							</article>
						{/each}
					</div>
				{/if}
			{/if}
		</div>
	</div>
</section>
