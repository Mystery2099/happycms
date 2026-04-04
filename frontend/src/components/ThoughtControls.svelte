<script lang="ts">
	import { onMount } from 'svelte';

	let { categories = [] } = $props<{ categories?: string[] }>();

	let search = $state('');
	let category = $state('all');
	let view = $state<'grid' | 'table'>('grid');
	let total = $state(0);
	let visible = $state(0);
	let isMobile = $state(true);

	let library: HTMLElement | null = null;
	let tableWrapper: HTMLElement | null = null;
	let cardGrid: HTMLElement | null = null;
	let items: HTMLElement[] = [];

	const MOBILE_BREAKPOINT = 768;

	function checkMobile() {
		return window.innerWidth < MOBILE_BREAKPOINT;
	}

	function applyView() {
		if (!library || !tableWrapper || !cardGrid) return;

		library.dataset.view = view;
		tableWrapper.hidden = view !== 'table';
		cardGrid.hidden = view !== 'grid';
		cardGrid.classList.toggle('hidden', view !== 'grid');
		cardGrid.classList.toggle('grid', view === 'grid');
	}

	function applyFilters() {
		const normalizedSearch = search.trim().toLowerCase();
		const normalizedCategory = category.toLowerCase();
		const visibleIds = new Set<string>();

		items.forEach((item) => {
			const haystack = [
				item.dataset.title ?? '',
				item.dataset.author ?? '',
				item.dataset.category ?? '',
				item.dataset.thought ?? ''
			].join(' ');

			const matchesSearch = normalizedSearch === '' || haystack.includes(normalizedSearch);
			const matchesCategory =
				normalizedCategory === 'all' || (item.dataset.category ?? '') === normalizedCategory;
			const matches = matchesSearch && matchesCategory;

			item.hidden = !matches;
			if (matches) {
				visibleIds.add(item.dataset.recordId ?? '');
			}
		});

		visible = visibleIds.size;
	}

	onMount(() => {
		library = document.querySelector<HTMLElement>('[data-thought-library]');
		if (!library) return;

		tableWrapper = library.children[0] as HTMLElement | null;
		cardGrid = document.querySelector<HTMLElement>('[data-card-grid]');
		items = Array.from(document.querySelectorAll<HTMLElement>('[data-thought-item]'));
		total = new Set(items.map((item) => item.dataset.recordId ?? '')).size;
		visible = total;

		// Set initial view based on viewport
		isMobile = checkMobile();
		view = isMobile ? 'grid' : 'table';

		applyView();

		// Listen for resize to auto-switch on mobile/desktop boundary
		const handleResize = () => {
			const nowMobile = checkMobile();
			if (nowMobile !== isMobile) {
				isMobile = nowMobile;
				// Force grid view when switching to mobile
				if (isMobile) {
					view = 'grid';
				}
			}
		};

		window.addEventListener('resize', handleResize);
		return () => window.removeEventListener('resize', handleResize);
	});

	$effect(() => {
		applyView();
	});

	$effect(() => {
		applyFilters();
	});
</script>

<div
	class="border-mist flex flex-col gap-4 border-y py-4 sm:flex-row sm:items-center sm:justify-between"
>
	<div class="flex flex-wrap items-center gap-3">
		<span class="text-stone text-sm">{visible} of {total} shown</span>

		<div class="border-mist flex items-center gap-1 border-l pl-3">
			{#if !isMobile}
				<button
					type="button"
					class="control-btn"
					class:active={view === 'table'}
					onclick={() => (view = 'table')}
				>
					Table
				</button>
			{/if}
			<button
				type="button"
				class="control-btn"
				class:active={view === 'grid'}
				onclick={() => (view = 'grid')}
			>
				Grid
			</button>
		</div>
	</div>

	<div class="flex flex-col gap-3 sm:flex-row">
		<input
			bind:value={search}
			type="search"
			placeholder="Filter results..."
			class="input-minimal w-full sm:w-48 lg:w-64"
		/>

		<select bind:value={category} class="input-minimal w-full bg-transparent sm:w-44">
			<option value="all">All categories</option>
			{#each categories as entry}
				<option value={entry.toLowerCase()}>{entry}</option>
			{/each}
		</select>
	</div>
</div>
