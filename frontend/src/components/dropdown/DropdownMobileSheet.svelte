<script lang="ts">
	import { Check, ChevronDown, X } from '@lucide/svelte';
	import type { DropdownOption } from '../../lib/dropdown';
	import MobileSheet from '../MobileSheet.svelte';

	interface Props {
		options: DropdownOption[];
		value: string;
		selectedLabel: string;
		listboxId: string;
		sheetInstanceId: string;
		mobileSheetTitleId: string;
		isVisible: boolean;
		isOverlayVisible: boolean;
		isDragging: boolean;
		isExpanded: boolean;
		shouldHintMoreContent: boolean;
		ariaDescribedby?: string;
		listAriaLabel?: string;
		ariaLabel?: string;
		sheetElement?: HTMLElement | null;
		handleElement?: HTMLButtonElement | null;
		onClose: () => void;
		onSelect: (value: string) => void;
		onHandlePointerDown: (event: PointerEvent) => void;
		onHandlePointerMove: (event: PointerEvent) => void;
		onHandlePointerEnd: (event: PointerEvent) => void;
		onHandleClick: (event: MouseEvent) => void;
		onToggleSheetExpansion: () => void;
	}

	let {
		options,
		value,
		selectedLabel,
		listboxId,
		sheetInstanceId,
		mobileSheetTitleId,
		isVisible,
		isOverlayVisible,
		isDragging,
		isExpanded,
		shouldHintMoreContent,
		ariaDescribedby,
		listAriaLabel,
		ariaLabel,
		sheetElement = $bindable(),
		handleElement = $bindable(),
		onClose,
		onSelect,
		onHandlePointerDown,
		onHandlePointerMove,
		onHandlePointerEnd,
		onHandleClick,
		onToggleSheetExpansion
	}: Props = $props();
</script>

	<MobileSheet
		isVisible={isVisible}
		isOverlayVisible={isOverlayVisible}
		panelId={listboxId}
		panelDataId={sheetInstanceId}
	closeLabel="Close options"
	ariaLabelledby={mobileSheetTitleId}
	ariaDescribedby={ariaDescribedby}
	bind:sheetElement
	initialFocusElement={handleElement}
	{onClose}
		panelClass="pointer-events-auto overflow-hidden rounded-t-[28px] border border-b-0 border-stone-200/80 bg-[#faf9f7] shadow-[0_-12px_40px_rgba(15,23,42,0.18)] dark:border-slate-700 dark:bg-slate-900 {isDragging ? 'transition-none' : 'transition-transform duration-220 ease-out'}"
	>
		<div class="sticky top-0 z-10 bg-[#faf9f7]/95 backdrop-blur-sm dark:bg-slate-900/95">
			<button
				bind:this={handleElement}
				type="button"
				class="mx-auto mt-3 flex h-8 w-full cursor-grab items-center justify-center touch-none active:cursor-grabbing"
				onpointerdown={onHandlePointerDown}
				onpointermove={onHandlePointerMove}
				onpointerup={onHandlePointerEnd}
				onpointercancel={onHandlePointerEnd}
				onclick={onHandleClick}
				aria-label={isExpanded ? 'Collapse options panel' : 'Expand options panel'}
			>
				<span class="h-1.5 w-12 rounded-full bg-stone-300/80 dark:bg-slate-600"></span>
			</button>
			<div class="flex items-center justify-between border-b border-stone-200/70 px-5 pb-3 pt-2 dark:border-slate-800">
				<div>
					<p class="text-xs font-semibold uppercase tracking-[0.2em] text-stone dark:text-slate-400">
						Choose
					</p>
					<h2 id={mobileSheetTitleId} class="text-base font-medium text-ink dark:text-slate-50">
						{listAriaLabel ?? ariaLabel ?? selectedLabel}
					</h2>
					{#if shouldHintMoreContent}
						<p class="mt-1 flex items-center gap-1.5 text-xs font-medium text-coral dark:text-rose-300">
							<span class="inline-flex h-5 w-5 items-center justify-center rounded-full bg-coral/10 dark:bg-rose-300/10">
								<ChevronDown size={12} class="-rotate-90" />
							</span>
							Pull up to reveal more options
						</p>
					{/if}
				</div>
				<div class="flex items-center gap-2">
					<button
						type="button"
						class="rounded-full px-3 py-2 text-xs font-semibold uppercase tracking-[0.18em] text-stone transition-colors hover:text-ink dark:text-slate-300 dark:hover:text-slate-50"
						onclick={onToggleSheetExpansion}
						aria-label={isExpanded ? 'Collapse options panel' : 'Expand options panel'}
					>
						{isExpanded ? 'Peek' : 'Expand'}
					</button>
					<button
						type="button"
						class="flex h-11 w-11 items-center justify-center rounded-2xl bg-stone-200/70 text-stone transition-colors hover:bg-stone-300/80 hover:text-ink dark:bg-slate-800 dark:text-slate-300 dark:hover:bg-slate-700 dark:hover:text-slate-50"
						onclick={onClose}
						aria-label="Close options"
					>
						<X size={18} />
					</button>
				</div>
			</div>
		</div>

		<div class="dropdown-sheet-scroll relative px-4 pb-[max(1rem,env(safe-area-inset-bottom))]">
			<div
				class="space-y-2 pb-4"
				role="listbox"
				aria-labelledby={mobileSheetTitleId}
				aria-describedby={ariaDescribedby}
			>
				{#each options as option (option.value)}
					{@const Icon = option.icon}
					<button
						type="button"
						class="flex min-h-14 w-full items-center justify-between gap-3 rounded-2xl border px-4 py-3 text-left transition-colors {value === option.value
							? 'border-coral bg-coral/10 text-ink dark:border-rose-400 dark:bg-rose-400/15 dark:text-slate-50'
							: 'border-stone-200/80 bg-white/80 text-stone hover:border-stone-300 hover:text-ink dark:border-slate-700 dark:bg-slate-800/90 dark:text-slate-300 dark:hover:border-slate-600 dark:hover:text-slate-50'}"
						onclick={() => onSelect(option.value)}
						role="option"
						aria-selected={value === option.value}
					>
						<span class="flex min-w-0 items-center gap-3">
							{#if Icon}
								<span class="flex h-10 w-10 items-center justify-center rounded-xl bg-stone-100 text-stone dark:bg-slate-700 dark:text-slate-200">
									<Icon size={18} />
								</span>
							{/if}
							<span class="truncate text-sm font-medium capitalize">{option.label}</span>
						</span>
						{#if value === option.value}
							<span class="flex h-8 w-8 items-center justify-center rounded-full bg-coral text-white dark:bg-rose-400 dark:text-slate-950">
								<Check size={16} />
							</span>
						{/if}
					</button>
				{/each}
			</div>

			{#if shouldHintMoreContent}
				<div
					class="pointer-events-none absolute inset-x-4 bottom-0 h-24 rounded-t-[24px] bg-gradient-to-t from-[#faf9f7] via-[#faf9f7]/92 to-transparent dark:from-slate-900 dark:via-slate-900/90"
					aria-hidden="true"
				></div>
			{/if}
		</div>
	</MobileSheet>
