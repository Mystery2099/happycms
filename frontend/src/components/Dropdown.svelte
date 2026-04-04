<script lang="ts">
	import { ChevronDown, Check, X } from '@lucide/svelte';
	import Hammer from 'hammerjs';
	import type { Component } from 'svelte';
	import { onMount, tick } from 'svelte';

	interface Option {
		value: string;
		label: string;
		icon?: Component;
	}

	interface Props {
		options: Option[];
		value: string;
		placeholder?: string;
		class?: string;
		triggerClass?: string;
		menuClass?: string;
		optionClass?: string;
		ariaLabel?: string;
		listAriaLabel?: string;
		showIconInTrigger?: boolean;
		showLabelInTrigger?: boolean;
		mobileBreakpoint?: number;
		onChange?: (value: string) => void;
	}

	let {
		options,
		value = $bindable(),
		placeholder = 'Select...',
		class: className = '',
		triggerClass = '',
		menuClass = '',
		optionClass = '',
		ariaLabel,
		listAriaLabel,
		showIconInTrigger = false,
		showLabelInTrigger = true,
		mobileBreakpoint = 768,
		onChange
	}: Props = $props();

	let isOpen = $state(false);
	let viewportWidth = $state(1024);
	let viewportHeight = $state(900);
	let isExpanded = $state(false);
	let dragOffset = $state(0);
	let selectedOption = $derived(options.find((opt) => opt.value === value));
	let selectedLabel = $derived(selectedOption?.label ?? placeholder);
	let SelectedIcon = $derived(selectedOption?.icon);
	let isMobile = $derived(viewportWidth < mobileBreakpoint);
	let sheetElement = $state<HTMLElement | null>(null);
	let dragSurfaceElement = $state<HTMLElement | null>(null);
	let handleElement = $state<HTMLButtonElement | null>(null);
	let styleElement: HTMLStyleElement | null = null;
	const sheetInstanceId = `dropdown-sheet-${Math.random().toString(36).slice(2, 10)}`;

	const defaultTriggerClass =
		'input-minimal hover:border-stone flex w-full items-center justify-between gap-2 text-left transition-all duration-300 focus:translate-y-[-1px]';
	const defaultMenuClass =
		'border-mist absolute top-full right-0 left-0 z-50 mt-1 transform rounded-lg border bg-white shadow-lg dark:border-slate-700 dark:bg-slate-800';
	const defaultOptionClass =
		'hover:bg-mist/30 flex w-full items-center gap-2 px-4 py-2.5 text-left text-sm capitalize transition-colors duration-150 dark:hover:bg-slate-700/50';

	function toggle() {
		isOpen = !isOpen;
		if (isOpen) {
			isExpanded = false;
			dragOffset = 0;
		}
	}

	function close() {
		isOpen = false;
		isExpanded = false;
		dragOffset = 0;
	}

	function selectOption(optionValue: string) {
		value = optionValue;
		close();
		onChange?.(optionValue);
	}

	function handleKeydown(event: KeyboardEvent) {
		if (event.key === 'Escape') {
			close();
		}
	}

	function handleClickOutside(event: MouseEvent) {
		if (isMobile || !isOpen) return;

		const target = event.target as HTMLElement;
		if (!target.closest('[data-dropdown-root]')) {
			close();
		}
	}

	function getExpandedTopOffset() {
		return 8;
	}

	function getMinimumVisibleHeight() {
		return Math.max(viewportHeight * 0.56, 360);
	}

	function getMaximumSheetHeight() {
		return Math.max(viewportHeight - getExpandedTopOffset(), getMinimumVisibleHeight());
	}

	function getCollapsedOffset() {
		return Math.max(getMaximumSheetHeight() - getMinimumVisibleHeight(), 0);
	}

	function getSheetTranslateY() {
		const restingOffset = isExpanded ? 0 : getCollapsedOffset();
		return Math.max(0, restingOffset + dragOffset);
	}

	function updateSheetStyles() {
		if (!styleElement) return;

		if (!isOpen || !isMobile) {
			styleElement.textContent = '';
			return;
		}

		const maxSheetHeight = getMaximumSheetHeight();
		const scrollHeight = Math.max(maxSheetHeight - 92, 220);

		styleElement.textContent = `
			[data-dropdown-sheet-id="${sheetInstanceId}"] {
				height: ${maxSheetHeight}px;
				max-height: calc(100dvh - ${getExpandedTopOffset()}px);
				transform: translateY(${getSheetTranslateY()}px);
			}

			[data-dropdown-sheet-id="${sheetInstanceId}"] .dropdown-sheet-scroll {
				max-height: ${scrollHeight}px;
			}
		`;
	}

	onMount(() => {
		const nonce = document
			.querySelector<HTMLMetaElement>('meta[name="csp-nonce"]')
			?.getAttribute('content');

		styleElement = document.createElement('style');
		if (nonce) {
			styleElement.setAttribute('nonce', nonce);
		}

		document.head.appendChild(styleElement);
		updateSheetStyles();

		return () => {
			styleElement?.remove();
			styleElement = null;
		};
	});

	$effect(() => {
		if (!isMobile || !isOpen) return;

		const previousOverflow = document.body.style.overflow;
		document.body.style.overflow = 'hidden';

		return () => {
			document.body.style.overflow = previousOverflow;
		};
	});

	$effect(() => {
		if (!isMobile || !isOpen || !dragSurfaceElement || !sheetElement) return;

		let startOffset = getSheetTranslateY();
		const snapThreshold = 80;
		const closeThreshold = Math.max(getCollapsedOffset() + 140, viewportHeight * 0.72);
		const hammer = new Hammer.Manager(dragSurfaceElement, {
			touchAction: 'none',
			recognizers: [[Hammer.Pan, { direction: Hammer.DIRECTION_VERTICAL, threshold: 0 }]]
		});

		const onPanStart = () => {
			startOffset = getSheetTranslateY();
			sheetElement?.classList.add('transition-none');
		};

		const onPanMove = (event: HammerInput) => {
			const nextOffset = Math.max(0, startOffset + event.deltaY);
			const collapsedOffset = getCollapsedOffset();
			const resistance = nextOffset > collapsedOffset ? (nextOffset - collapsedOffset) * 0.35 : 0;
			dragOffset = nextOffset - (isExpanded ? 0 : collapsedOffset) - resistance;
		};

		const onPanEnd = (event: HammerInput) => {
			sheetElement?.classList.remove('transition-none');

			const finalOffset = Math.max(0, startOffset + event.deltaY);
			const collapsedOffset = getCollapsedOffset();
			const wantsClose = finalOffset > closeThreshold && event.deltaY > 0;
			const wantsExpand =
				event.deltaY < -snapThreshold || finalOffset < collapsedOffset * 0.5;
			const wantsCollapse =
				event.deltaY > snapThreshold || finalOffset >= collapsedOffset * 0.5;

			dragOffset = 0;

			if (wantsClose) {
				close();
				return;
			}

			if (wantsExpand) {
				isExpanded = true;
				return;
			}

			if (wantsCollapse) {
				isExpanded = false;
			}
		};

		hammer.on('panstart', onPanStart);
		hammer.on('panmove', onPanMove);
		hammer.on('panend pancancel', onPanEnd);

		return () => {
			hammer.off('panstart', onPanStart);
			hammer.off('panmove', onPanMove);
			hammer.off('panend pancancel', onPanEnd);
			hammer.destroy();
		};
	});

	$effect(() => {
		if (!isOpen || !isMobile) return;

		tick().then(() => {
			handleElement?.focus();
		});
	});

	$effect(() => {
		updateSheetStyles();
	});
</script>

<svelte:window
	bind:innerWidth={viewportWidth}
	bind:innerHeight={viewportHeight}
	onclick={handleClickOutside}
	onkeydown={handleKeydown}
/>

<div class="relative {className}" data-dropdown-root>
	<button
		type="button"
		class={triggerClass || defaultTriggerClass}
		onclick={toggle}
		aria-expanded={isOpen}
		aria-haspopup={isMobile ? 'dialog' : 'listbox'}
		aria-label={ariaLabel}
	>
		<span class="flex min-w-0 items-center gap-2">
			{#if showIconInTrigger && SelectedIcon}
				<SelectedIcon size={16} />
			{/if}
			{#if showLabelInTrigger}
				<span class="truncate capitalize">{selectedLabel}</span>
			{/if}
		</span>
		<ChevronDown size={16} class="shrink-0 transition-transform duration-200 {isOpen ? 'rotate-180' : ''}" />
	</button>

	{#if isOpen && !isMobile}
		<div
			class={menuClass || defaultMenuClass}
			role="listbox"
			aria-label={listAriaLabel ?? ariaLabel ?? selectedLabel}
		>
			<div class="max-h-60 overflow-y-auto py-1">
				{#each options as option}
					{@const Icon = option.icon}
					<button
						type="button"
						class="{optionClass || defaultOptionClass} {value === option.value
							? 'text-ink bg-mist/20 dark:bg-slate-700/30'
							: 'text-stone'}"
						onclick={() => selectOption(option.value)}
						role="option"
						aria-selected={value === option.value}
					>
						{#if Icon}
							<Icon size={16} />
						{/if}
						{option.label}
					</button>
				{/each}
			</div>
		</div>
	{/if}
</div>

{#if isOpen && isMobile}
	<div class="fixed inset-0 z-[140]" aria-hidden="true">
		<button
			type="button"
			class="absolute inset-0 bg-slate-950/45 backdrop-blur-[2px]"
			onclick={close}
			tabindex="-1"
			aria-label="Close options"
		></button>
	</div>

	<div
		bind:this={sheetElement}
		data-dropdown-sheet-id={sheetInstanceId}
		class="fixed inset-x-0 bottom-0 z-[150] overflow-hidden rounded-t-[28px] border border-b-0 border-stone-200/80 bg-[#faf9f7] shadow-[0_-12px_40px_rgba(15,23,42,0.18)] transition-transform duration-300 ease-out dark:border-slate-700 dark:bg-slate-900"
		role="dialog"
		aria-modal="true"
		aria-label={listAriaLabel ?? ariaLabel ?? selectedLabel}
	>
		<div
			bind:this={dragSurfaceElement}
			class="dropdown-drag-surface sticky top-0 z-10 touch-none select-none bg-[#faf9f7]/95 backdrop-blur-sm dark:bg-slate-900/95"
		>
			<button
				bind:this={handleElement}
				type="button"
				class="mx-auto mt-3 flex h-8 w-full cursor-grab items-center justify-center active:cursor-grabbing"
				onclick={() => (isExpanded = !isExpanded)}
				aria-label={isExpanded ? 'Collapse options panel' : 'Expand options panel'}
			>
				<span class="h-1.5 w-12 rounded-full bg-stone-300/80 dark:bg-slate-600"></span>
			</button>
			<div class="flex items-center justify-between border-b border-stone-200/70 px-5 pb-3 pt-2 dark:border-slate-800">
				<div>
					<p class="text-xs font-semibold uppercase tracking-[0.2em] text-stone dark:text-slate-400">
						Choose
					</p>
					<h2 class="text-base font-medium text-ink dark:text-slate-50">
						{listAriaLabel ?? ariaLabel ?? selectedLabel}
					</h2>
				</div>
				<button
					type="button"
					class="flex h-11 w-11 items-center justify-center rounded-2xl bg-stone-200/70 text-stone transition-colors hover:bg-stone-300/80 hover:text-ink dark:bg-slate-800 dark:text-slate-300 dark:hover:bg-slate-700 dark:hover:text-slate-50"
					onclick={close}
					aria-label="Close options"
				>
					<X size={18} />
				</button>
			</div>
		</div>

		<div
			class="dropdown-sheet-scroll overflow-y-auto px-4 pb-[max(1rem,env(safe-area-inset-bottom))]"
		>
			<div class="space-y-2 pb-4">
				{#each options as option}
					{@const Icon = option.icon}
					<button
						type="button"
						class="flex min-h-14 w-full items-center justify-between gap-3 rounded-2xl border px-4 py-3 text-left transition-colors {value === option.value
							? 'border-coral bg-coral/10 text-ink dark:border-rose-400 dark:bg-rose-400/15 dark:text-slate-50'
							: 'border-stone-200/80 bg-white/80 text-stone hover:border-stone-300 hover:text-ink dark:border-slate-700 dark:bg-slate-800/90 dark:text-slate-300 dark:hover:border-slate-600 dark:hover:text-slate-50'}"
						onclick={() => selectOption(option.value)}
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
		</div>
	</div>
{/if}
