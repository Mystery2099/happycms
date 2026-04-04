<script lang="ts">
	import { ChevronDown, Check, X } from '@lucide/svelte';
	import type HammerType from 'hammerjs';
	import type { Component } from 'svelte';
	import { onMount, tick } from 'svelte';
	import type { HammerInput } from 'hammerjs';

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
		ariaLabelledby?: string;
		ariaDescribedby?: string;
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
		ariaLabelledby,
		ariaDescribedby,
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
	let isDragging = $state(false);
	let highlightedIndex = $state(-1);
	let selectedOption = $derived(options.find((opt) => opt.value === value));
	let selectedLabel = $derived(selectedOption?.label ?? placeholder);
	let SelectedIcon = $derived(selectedOption?.icon);
	let isMobile = $derived(viewportWidth < mobileBreakpoint);
	let triggerElement = $state<HTMLButtonElement | null>(null);
	let listboxElement = $state<HTMLDivElement | null>(null);
	let sheetElement = $state<HTMLElement | null>(null);
	let dragSurfaceElement = $state<HTMLElement | null>(null);
	let handleElement = $state<HTMLButtonElement | null>(null);
	let optionElements = $state<HTMLButtonElement[]>([]);
	let styleElement: HTMLStyleElement | null = null;
	let sheetRule: CSSStyleRule | null = null;
	let scrollRule: CSSStyleRule | null = null;
	const sheetInstanceId = `dropdown-sheet-${Math.random().toString(36).slice(2, 10)}`;
	const listboxId = `dropdown-listbox-${Math.random().toString(36).slice(2, 10)}`;
	const triggerId = `dropdown-trigger-${Math.random().toString(36).slice(2, 10)}`;
	let currentTranslateY = 0;
	let shouldHintMoreContent = $derived(!isExpanded && options.length > 6);
	let hammerModulePromise: Promise<typeof import('hammerjs')> | null = null;

	const defaultTriggerClass =
		'input-minimal hover:border-stone flex w-full items-center justify-between gap-2 text-left transition-all duration-300 focus:translate-y-[-1px]';
	const defaultMenuClass =
		'border-mist absolute top-full right-0 left-0 z-50 mt-1 transform rounded-lg border bg-white shadow-lg dark:border-slate-700 dark:bg-slate-800';
	const defaultOptionClass =
		'hover:bg-mist/30 flex w-full items-center gap-2 px-4 py-2.5 text-left text-sm capitalize transition-colors duration-150 dark:hover:bg-slate-700/50';

	function getSelectedIndex() {
		const selectedIndex = options.findIndex((option) => option.value === value);
		return selectedIndex >= 0 ? selectedIndex : 0;
	}

	function focusDesktopOption(index: number) {
		tick().then(() => {
			optionElements[index]?.focus();
		});
	}

	function openDropdown({ focusSelectedOption = false }: { focusSelectedOption?: boolean } = {}) {
		isOpen = true;
		isExpanded = false;
		currentTranslateY = getCollapsedOffset();
		highlightedIndex = getSelectedIndex();

		if (!isMobile && focusSelectedOption) {
			focusDesktopOption(highlightedIndex);
		}
	}

	function toggle() {
		if (isOpen) {
			close({ restoreFocus: false });
			return;
		}

		openDropdown();
	}

	function focusTrigger() {
		tick().then(() => {
			requestAnimationFrame(() => {
				triggerElement?.focus();
				setTimeout(() => {
					triggerElement?.focus();
				}, 60);
			});
		});
	}

	function getSheetFocusableElements() {
		if (!sheetElement) return [] as HTMLElement[];

		return Array.from(
			sheetElement.querySelectorAll<HTMLElement>(
				'button:not([disabled]), [href], input:not([disabled]), select:not([disabled]), textarea:not([disabled]), [tabindex]:not([tabindex="-1"])'
			)
		).filter((element) => !element.hasAttribute('hidden'));
	}

	function trapSheetFocus(event: KeyboardEvent) {
		if (event.key !== 'Tab') return;

		const focusableElements = getSheetFocusableElements();
		if (focusableElements.length === 0) {
			event.preventDefault();
			return;
		}

		const firstElement = focusableElements[0];
		const lastElement = focusableElements[focusableElements.length - 1];
		const activeElement = document.activeElement as HTMLElement | null;
		const focusIsInsideSheet = !!(activeElement && sheetElement?.contains(activeElement));

		if (!focusIsInsideSheet) {
			event.preventDefault();
			(event.shiftKey ? lastElement : firstElement).focus();
			return;
		}

		if (event.shiftKey && activeElement === firstElement) {
			event.preventDefault();
			lastElement.focus();
			return;
		}

		if (!event.shiftKey && activeElement === lastElement) {
			event.preventDefault();
			firstElement.focus();
		}
	}

	function keepFocusInsideSheet(event: FocusEvent) {
		if (!isMobile || !isOpen || !sheetElement) return;

		const nextTarget = event.target as HTMLElement | null;
		if (!nextTarget || sheetElement.contains(nextTarget)) return;

		const [firstElement] = getSheetFocusableElements();
		(firstElement ?? handleElement)?.focus();
	}

	function close({ restoreFocus = isMobile } = {}) {
		isOpen = false;
		isExpanded = false;
		isDragging = false;
		highlightedIndex = -1;
		currentTranslateY = 0;
		if (restoreFocus) {
			focusTrigger();
		}
	}

	function selectOption(optionValue: string) {
		value = optionValue;
		highlightedIndex = options.findIndex((option) => option.value === optionValue);
		close();
		onChange?.(optionValue);
	}

	function moveHighlight(step: number) {
		if (options.length === 0) return;

		const startIndex = highlightedIndex >= 0 ? highlightedIndex : getSelectedIndex();
		const nextIndex = (startIndex + step + options.length) % options.length;
		highlightedIndex = nextIndex;
		focusDesktopOption(nextIndex);
	}

	function jumpHighlight(index: number) {
		if (options.length === 0) return;

		const boundedIndex = Math.max(0, Math.min(index, options.length - 1));
		highlightedIndex = boundedIndex;
		focusDesktopOption(boundedIndex);
	}

	function handleTriggerKeydown(event: KeyboardEvent) {
		const opensDropdown = event.key === 'ArrowDown' || event.key === 'ArrowUp' || event.key === 'Enter' || event.key === ' ';
		if (!opensDropdown) return;

		event.preventDefault();

		if (!isOpen) {
			openDropdown({ focusSelectedOption: !isMobile });
		}

		if (isMobile) return;

		if (event.key === 'ArrowUp') {
			jumpHighlight(options.length - 1);
			return;
		}

		if (event.key === 'ArrowDown') {
			jumpHighlight(getSelectedIndex());
		}
	}

	function handleDesktopOptionKeydown(event: KeyboardEvent, index: number) {
		if (event.key === 'ArrowDown') {
			event.preventDefault();
			moveHighlight(1);
			return;
		}

		if (event.key === 'ArrowUp') {
			event.preventDefault();
			moveHighlight(-1);
			return;
		}

		if (event.key === 'Home') {
			event.preventDefault();
			jumpHighlight(0);
			return;
		}

		if (event.key === 'End') {
			event.preventDefault();
			jumpHighlight(options.length - 1);
			return;
		}

		if (event.key === 'Enter' || event.key === ' ') {
			event.preventDefault();
			selectOption(options[index]?.value ?? value);
			return;
		}

		if (event.key === 'Escape') {
			event.preventDefault();
			close();
		}
	}

	function handleKeydown(event: KeyboardEvent) {
		if (!isOpen) return;

		if (isMobile && isOpen) {
			trapSheetFocus(event);
		}

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

	function getRestingTranslateY() {
		return isExpanded ? 0 : getCollapsedOffset();
	}

	function applySheetTranslateY(nextTranslateY: number) {
		currentTranslateY = Math.max(0, nextTranslateY);
		if (sheetRule) {
			sheetRule.style.transform = `translateY(${currentTranslateY}px)`;
		}
	}

	function updateSheetStyles() {
		if (!sheetRule || !scrollRule) return;

		if (!isOpen || !isMobile) {
			sheetRule.style.cssText = '';
			scrollRule.style.cssText = '';
			return;
		}

		const maxSheetHeight = getMaximumSheetHeight();
		const scrollHeight = Math.max(maxSheetHeight - 92, 220);

		sheetRule.style.height = `${maxSheetHeight}px`;
		sheetRule.style.maxHeight = `calc(100dvh - ${getExpandedTopOffset()}px)`;
		applySheetTranslateY(isDragging ? currentTranslateY : getRestingTranslateY());

		scrollRule.style.maxHeight = `${scrollHeight}px`;
	}

	function loadHammerModule() {
		hammerModulePromise ??= import('hammerjs');
		return hammerModulePromise;
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
		const stylesheet = styleElement.sheet as CSSStyleSheet | null;
		if (stylesheet) {
			const sheetSelector = `[data-dropdown-sheet-id="${sheetInstanceId}"]`;
			const scrollSelector = `${sheetSelector} .dropdown-sheet-scroll`;
			stylesheet.insertRule(`${sheetSelector} {}`, 0);
			stylesheet.insertRule(`${scrollSelector} {}`, 1);
			sheetRule = stylesheet.cssRules[0] as CSSStyleRule;
			scrollRule = stylesheet.cssRules[1] as CSSStyleRule;
		}
		updateSheetStyles();

		return () => {
			styleElement?.remove();
			styleElement = null;
			sheetRule = null;
			scrollRule = null;
		};
	});

	$effect(() => {
		if (!isMobile || !isOpen) return;

		const previousOverflow = document.body.style.overflow;
		document.body.style.overflow = 'hidden';
		document.addEventListener('focusin', keepFocusInsideSheet);

		return () => {
			document.body.style.overflow = previousOverflow;
			document.removeEventListener('focusin', keepFocusInsideSheet);
		};
	});

	$effect(() => {
		if (!isMobile || !isOpen || !dragSurfaceElement || !sheetElement) return;

		let cancelled = false;
		let hammer: HammerType.Manager | null = null;
		let startOffset = getRestingTranslateY();
		const snapThreshold = 80;
		const closeThreshold = Math.max(getCollapsedOffset() + 140, viewportHeight * 0.72);
		const onPanStart = () => {
			startOffset = currentTranslateY;
			isDragging = true;
			applySheetTranslateY(startOffset);
		};

		const onPanMove = (event: HammerInput) => {
			const nextOffset = Math.max(0, startOffset + event.deltaY);
			const collapsedOffset = getCollapsedOffset();
			const resistance = nextOffset > collapsedOffset ? (nextOffset - collapsedOffset) * 0.35 : 0;
			applySheetTranslateY(nextOffset - resistance);
		};

		const onPanEnd = (event: HammerInput) => {
			const finalOffset = currentTranslateY;
			const collapsedOffset = getCollapsedOffset();
			const wantsClose = finalOffset > closeThreshold && event.deltaY > 0;
			const wantsExpand =
				event.deltaY < -snapThreshold || finalOffset < collapsedOffset * 0.5;
			const wantsCollapse =
				event.deltaY > snapThreshold || finalOffset >= collapsedOffset * 0.5;

			if (wantsClose) {
				close();
				return;
			}

			if (wantsExpand) {
				isExpanded = true;
			} else if (wantsCollapse) {
				isExpanded = false;
			}

			isDragging = false;
			applySheetTranslateY(getRestingTranslateY());
		};

		loadHammerModule().then((Hammer) => {
			if (cancelled) return;

			hammer = new Hammer.default.Manager(dragSurfaceElement, {
				touchAction: 'none',
				recognizers: [
					[Hammer.default.Pan, { direction: Hammer.default.DIRECTION_VERTICAL, threshold: 0 }]
				]
			});

			hammer.on('panstart', onPanStart);
			hammer.on('panmove', onPanMove);
			hammer.on('panend pancancel', onPanEnd);
		});

		return () => {
			cancelled = true;
			if (!hammer) return;

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

	$effect(() => {
		if (!isOpen || isMobile) return;

		highlightedIndex = getSelectedIndex();
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
				bind:this={triggerElement}
				type="button"
				class={triggerClass || defaultTriggerClass}
				onclick={toggle}
				onkeydown={handleTriggerKeydown}
				id={triggerId}
				aria-controls={isOpen ? listboxId : undefined}
				aria-describedby={ariaDescribedby}
				aria-expanded={isOpen}
				aria-haspopup={isMobile ? 'dialog' : 'listbox'}
				aria-label={ariaLabel}
				aria-labelledby={ariaLabelledby}
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
				bind:this={listboxElement}
				class={menuClass || defaultMenuClass}
				id={listboxId}
				role="listbox"
				aria-label={listAriaLabel ?? ariaLabel ?? selectedLabel}
				aria-labelledby={ariaLabelledby}
				aria-describedby={ariaDescribedby}
			>
				<div class="max-h-60 overflow-y-auto py-1">
					{#each options as option, index}
						{@const Icon = option.icon}
						<button
							bind:this={optionElements[index]}
							type="button"
							class="{optionClass || defaultOptionClass} {value === option.value
								? 'text-ink bg-mist/20 dark:bg-slate-700/30'
								: 'text-stone'}"
							onclick={() => selectOption(option.value)}
							onkeydown={(event) => handleDesktopOptionKeydown(event, index)}
							role="option"
							tabindex={highlightedIndex === index ? 0 : -1}
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
				id={listboxId}
			class="fixed inset-x-0 bottom-0 z-[150] overflow-hidden rounded-t-[28px] border border-b-0 border-stone-200/80 bg-[#faf9f7] shadow-[0_-12px_40px_rgba(15,23,42,0.18)] transition-transform duration-300 ease-out dark:border-slate-700 dark:bg-slate-900"
			class:dropdown-sheet--dragging={isDragging}
		role="dialog"
		aria-modal="true"
		aria-label={listAriaLabel ?? ariaLabel ?? selectedLabel}
		aria-labelledby={ariaLabelledby}
		aria-describedby={ariaDescribedby}
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
					{#if shouldHintMoreContent}
						<p class="mt-1 flex items-center gap-1.5 text-xs font-medium text-coral dark:text-rose-300">
							<span class="inline-flex h-5 w-5 items-center justify-center rounded-full bg-coral/10 dark:bg-rose-300/10">
								<ChevronDown size={12} class="-rotate-90" />
							</span>
							Pull up to reveal more options
						</p>
					{/if}
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
			class="dropdown-sheet-scroll relative overflow-y-auto px-4 pb-[max(1rem,env(safe-area-inset-bottom))]"
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

			{#if shouldHintMoreContent}
				<div
					class="pointer-events-none absolute inset-x-4 bottom-0 h-24 rounded-t-[24px] bg-gradient-to-t from-[#faf9f7] via-[#faf9f7]/92 to-transparent dark:from-slate-900 dark:via-slate-900/90"
					aria-hidden="true"
				></div>
				<div
					class="pointer-events-none absolute inset-x-0 bottom-4 flex justify-center"
					aria-hidden="true"
				>
					<div class="flex items-center gap-2 rounded-full border border-stone-200/80 bg-white/92 px-3 py-1.5 text-xs font-medium text-stone shadow-sm dark:border-slate-700 dark:bg-slate-800/92 dark:text-slate-300">
						<span class="inline-flex h-5 w-5 items-center justify-center rounded-full bg-coral/10 text-coral dark:bg-rose-300/10 dark:text-rose-300">
							<ChevronDown size={12} class="animate-bounce [animation-duration:1.4s]" />
						</span>
						More below
					</div>
				</div>
			{/if}
		</div>
	</div>
{/if}

<style>
	.dropdown-sheet--dragging {
		transition: none;
	}
</style>
