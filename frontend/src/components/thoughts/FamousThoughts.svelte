<script lang="ts">
	import { onMount } from 'svelte';
	import { ChevronLeft, ChevronRight } from '@lucide/svelte';

	type Quote = {
		author: string;
		quote: string;
		category: string;
	};

	let { apiUrl = 'api/famous-thoughts.php' } = $props<{ apiUrl?: string }>();

	let loading = $state(true);
	let error = $state('');
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
		if (event.key === 'Enter' || event.key === ' ') {
			event.preventDefault();
			cycleQuote(direction);
		}
	}

	onMount(() => {
		fetch(apiUrl)
			.then((response) => {
				if (!response.ok) {
					throw new Error('Unable to load quotes');
				}

				return response.json();
			})
			.then((payload) => {
				quotes = payload.quotes ?? [];
				loading = false;
			})
			.catch((caught) => {
				error = caught instanceof Error ? caught.message : 'Unable to load quotes';
				loading = false;
			});
	});
</script>

<div class="grid gap-12 lg:grid-cols-2 lg:gap-16">
	<div>
		<h2 class="font-display text-2xl sm:text-3xl lg:text-4xl text-ink mb-4">Interactive Display</h2>
		<p class="text-stone mb-8">
			Three client-driven formatting changes demonstrate JavaScript interactivity:
		</p>

		<div class="space-y-6">
			<div class="group flex items-start gap-4">
				<span
					class="bg-coral flex h-8 w-8 flex-shrink-0 items-center justify-center text-sm font-medium text-white transition-transform duration-200 group-hover:scale-110"
					>1</span
				>
				<div class="transition-transform duration-200 group-hover:translate-x-1">
					<p class="text-ink font-medium">View Modes</p>
					<p class="text-stone text-sm">Toggle between table and grid layouts</p>
				</div>
			</div>

			<div class="group flex items-start gap-4">
				<span
					class="bg-coral flex h-8 w-8 flex-shrink-0 items-center justify-center text-sm font-medium text-white transition-transform duration-200 group-hover:scale-110"
					>2</span
				>
				<div class="transition-transform duration-200 group-hover:translate-x-1">
					<p class="text-ink font-medium">Density Control</p>
					<p class="text-stone text-sm">Adjust spacing for compact or comfortable viewing</p>
				</div>
			</div>

			<div class="group flex items-start gap-4">
				<span
					class="bg-coral flex h-8 w-8 flex-shrink-0 items-center justify-center text-sm font-medium text-white transition-transform duration-200 group-hover:scale-110"
					>3</span
				>
				<div class="transition-transform duration-200 group-hover:translate-x-1">
					<p class="text-ink font-medium">Theme Switching</p>
					<p class="text-stone text-sm">Toggle light, dark, or system-based visual styling</p>
				</div>
			</div>
		</div>
	</div>

	<div class="border-mist border-l pl-0 lg:pl-12">
		<div class="mb-6 flex items-center justify-between">
			<div>
				<p class="text-stone text-sm font-medium tracking-wider uppercase">Fetch API</p>
				<h3 class="font-display text-ink text-2xl">Famous Happy Thoughts</h3>
			</div>
			{#if !loading && !error}
				<span class="text-stone text-sm">{index + 1} / {quotes.length}</span>
			{/if}
		</div>

			{#if loading}
				<div class="py-12 text-center" aria-live="polite">
				<div class="relative inline-block">
					<div class="border-mist border-t-coral h-8 w-8 animate-spin rounded-full border-2"></div>
					<div
						class="border-mist/30 border-t-coral/50 absolute inset-0 h-8 w-8 animate-ping rounded-full border-2 opacity-20"
					></div>
				</div>
				<p class="text-stone mt-4 animate-pulse text-sm">Loading quotes...</p>
			</div>
			{:else if error}
				<div class="bg-coral/10 border-coral/20 border px-6 py-8" role="alert">
					<p class="text-coral text-sm">{error}</p>
				</div>
			{:else if currentQuote}
				<div class="relative" aria-live="polite" aria-atomic="true">
					<p class="sr-only">Showing quote {visibleIndex + 1} of {quotes.length}</p>
					<span
						class="quote-mark absolute -top-4 -left-2 transition-transform duration-500 hover:scale-110"
					aria-hidden="true"
				>"</span
				>
				<blockquote
					class={['font-display text-ink pl-6 text-2xl leading-relaxed lg:text-3xl transition-[opacity,filter] duration-250 ease-enter motion-reduce:transition-none', isTransitioning ? 'opacity-0 blur-[2px]' : 'opacity-100 blur-0']}
				>
					{currentQuote.quote}
				</blockquote>

				<div class={['mt-8 flex items-center justify-between gap-4 transition-opacity duration-250 ease-out motion-reduce:transition-none', isTransitioning ? 'opacity-0' : 'opacity-100']}>
					<div>
						<p class="text-ink font-medium">{currentQuote.author}</p>
						<p class="text-stone text-sm">{currentQuote.category}</p>
					</div>

					<div class="flex gap-2">
						<button
							type="button"
							class="border-mist hover:border-coral dark:hover:border-coral dark:hover:text-coral flex h-10 w-10 items-center justify-center border transition-[transform,background-color,border-color] duration-200 hover:-translate-x-0.5 active:scale-[0.97] ease-enter motion-reduce:transition-none dark:border-slate-600 dark:text-slate-300"
							onclick={() => cycleQuote(-1)}
							onkeydown={(e) => handleQuoteNavKeydown(e, -1)}
							aria-label="Previous quote"
						>
							<ChevronLeft size={16} />
						</button>
						<button
							type="button"
							class="bg-ink text-canvas hover:bg-coral dark:hover:bg-coral flex h-10 w-10 items-center justify-center transition-[transform,background-color] duration-200 hover:translate-x-0.5 active:scale-[0.97] ease-enter motion-reduce:transition-none dark:bg-slate-700 dark:text-slate-100"
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