<script lang="ts">
	import { onMount } from 'svelte';
	import { Table2, Grid3X3 } from '@lucide/svelte';
	import Dropdown from './Dropdown.svelte';

	let { categories = [] } = $props<{ categories?: string[] }>();

	let search = $state('');
	let category = $state('all');
	let view = $state<'grid' | 'table'>('grid');
	let density = $state<'compact' | 'comfortable'>('comfortable');
	let total = $state(0);
	let visible = $state(0);
	let isMobile = $state(true);

	let categoryOptions = $derived([
		{ value: 'all', label: 'All categories' },
		...categories.map((cat: string) => ({ value: cat.toLowerCase(), label: cat }))
	]);

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
		library.dataset.density = density;
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

		return () => {
			window.removeEventListener('resize', handleResize);
		};
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
		<span class="text-stone text-sm tabular-nums transition-all duration-300">
			<span
				class="inline-block transition-all duration-300 {visible === 0
					? 'opacity-50'
					: 'opacity-100'}">{visible}</span
			>
			<span class="text-mist">/</span>
			<span class="inline-block">{total}</span>
			<span class="ml-1">shown</span>
		</span>

		<div class="border-mist flex items-center gap-1 border-l pl-3">
			<div class="bg-mist/50 flex items-center rounded-lg p-0.5 dark:bg-slate-800/50">
				{#if !isMobile}
					<button
						type="button"
						class="flex items-center gap-1.5 rounded-md px-2.5 py-1.5 text-sm font-medium transition-all duration-200 active:scale-95"
						class:text-ink={view === 'table'}
						class:bg-white={view === 'table'}
						class:dark:bg-slate-700={view === 'table'}
						class:shadow-sm={view === 'table'}
						class:ring-2={view === 'table'}
						class:ring-coral={view === 'table'}
						class:ring-offset-1={view === 'table'}
						class:dark:ring-offset-slate-800={view === 'table'}
						class:text-stone={view !== 'table'}
						class:hover:text-ink={view !== 'table'}
						onclick={() => (view = 'table')}
						title="Table view"
					>
						<Table2
							size={16}
							class="transition-transform duration-200 {view === 'table' ? 'scale-110' : ''}"
						/>
						<span class="hidden sm:inline">Table</span>
					</button>
				{/if}
				<button
					type="button"
					class="flex items-center gap-1.5 rounded-md px-2.5 py-1.5 text-sm font-medium transition-all duration-200 active:scale-95"
					class:text-ink={view === 'grid'}
					class:bg-white={view === 'grid'}
					class:dark:bg-slate-700={view === 'grid'}
					class:shadow-sm={view === 'grid'}
					class:ring-2={view === 'grid'}
					class:ring-coral={view === 'grid'}
					class:ring-offset-1={view === 'grid'}
					class:dark:ring-offset-slate-800={view === 'grid'}
					class:text-stone={view !== 'grid'}
					class:hover:text-ink={view !== 'grid'}
					onclick={() => (view = 'grid')}
					title="Grid view"
				>
					<Grid3X3
						size={16}
						class="transition-transform duration-200 {view === 'grid' ? 'scale-110' : ''}"
					/>
					<span class="hidden sm:inline">Grid</span>
				</button>
			</div>
		</div>

		<div class="border-mist flex items-center gap-1 border-l pl-3">
			<div class="bg-mist/50 flex items-center rounded-lg p-0.5 dark:bg-slate-800/50">
				<button
					type="button"
					class="rounded-md px-3 py-1.5 text-sm font-medium transition-all duration-200 active:scale-95"
					class:text-ink={density === 'compact'}
					class:bg-white={density === 'compact'}
					class:dark:bg-slate-700={density === 'compact'}
					class:shadow-sm={density === 'compact'}
					class:ring-2={density === 'compact'}
					class:ring-coral={density === 'compact'}
					class:ring-offset-1={density === 'compact'}
					class:dark:ring-offset-slate-800={density === 'compact'}
					class:text-stone={density !== 'compact'}
					class:hover:text-ink={density !== 'compact'}
					onclick={() => (density = 'compact')}
					title="Compact view"
				>
					Compact
				</button>
				<button
					type="button"
					class="rounded-md px-3 py-1.5 text-sm font-medium transition-all duration-200 active:scale-95"
					class:text-ink={density === 'comfortable'}
					class:bg-white={density === 'comfortable'}
					class:dark:bg-slate-700={density === 'comfortable'}
					class:shadow-sm={density === 'comfortable'}
					class:ring-2={density === 'comfortable'}
					class:ring-coral={density === 'comfortable'}
					class:ring-offset-1={density === 'comfortable'}
					class:dark:ring-offset-slate-800={density === 'comfortable'}
					class:text-stone={density !== 'comfortable'}
					class:hover:text-ink={density !== 'comfortable'}
					onclick={() => (density = 'comfortable')}
					title="Comfortable view"
				>
					Comfortable
				</button>
			</div>
		</div>
	</div>

	<div class="flex flex-col gap-3 sm:flex-row">
		<div class="group relative">
			<input
				bind:value={search}
				type="search"
				placeholder="Filter results..."
				class="input-minimal w-full transition-all duration-300 focus:translate-y-[-1px] sm:w-48 lg:w-64"
			/>
			{#if search}
				<button
					type="button"
					class="text-stone hover:text-coral absolute top-1/2 right-2 -translate-y-1/2 opacity-0 transition-all duration-200 group-hover:opacity-100 focus:opacity-100"
					onclick={() => (search = '')}
					aria-label="Clear search"
				>
					<svg
						xmlns="http://www.w3.org/2000/svg"
						width="16"
						height="16"
						viewBox="0 0 24 24"
						fill="none"
						stroke="currentColor"
						stroke-width="2"
						stroke-linecap="round"
						stroke-linejoin="round"
						><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"
						></line></svg
					>
				</button>
			{/if}
		</div>

		<Dropdown
			options={categoryOptions}
			bind:value={category}
			class="sm:w-44"
			ariaLabel="Filter thoughts by category"
			listAriaLabel="Category filters"
		/>
	</div>
</div>
