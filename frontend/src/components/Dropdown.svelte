<script lang="ts">
	import { innerHeight, innerWidth } from 'svelte/reactivity/window';
	import { tick } from 'svelte';
	import DropdownDesktopList from './dropdown/DropdownDesktopList.svelte';
	import DropdownMobileSheet from './dropdown/DropdownMobileSheet.svelte';
	import DropdownTrigger from './dropdown/DropdownTrigger.svelte';
	import type { DropdownOption } from '../lib/dropdown';
	import { createMobileSheetManager } from '../lib/use-mobile-sheet.svelte';
	import { desktopSurfaceTriggerBaseClass } from '../lib/ui-classes';
	import { MOBILE_BREAKPOINT } from '../lib/sheet-constants';
	import { getCollapsedOffset, getMaximumSheetHeight, getRestingTranslateY } from '../lib/sheet-geometry';
	import { getSelectedIndex, moveHighlight, jumpHighlight } from '../lib/dropdown-keyboard';

	interface Props {
		options: DropdownOption[];
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
		mobileBreakpoint = MOBILE_BREAKPOINT,
		onChange
	}: Props = $props();

	const sheetInstanceId = `dropdown-sheet-${Math.random().toString(36).slice(2, 10)}`;
	const listboxId = `dropdown-listbox-${Math.random().toString(36).slice(2, 10)}`;
	const triggerId = `dropdown-trigger-${Math.random().toString(36).slice(2, 10)}`;
	const mobileSheetTitleId = `dropdown-sheet-title-${Math.random().toString(36).slice(2, 10)}`;

	const sheet = createMobileSheetManager({
		sheetDataId: sheetInstanceId,
		variant: 'dropdown',
		getViewportHeight: () => innerHeight.current ?? 900,
		getViewportWidth: () => innerWidth.current ?? 1024
	});

	let isOpen = $derived(sheet.isOpen);
	let isMobile = $derived((innerWidth.current ?? 1024) < mobileBreakpoint);
	let shouldHintMoreContent = $derived(!sheet.isExpanded && options.length > 6);
	let highlightedIndex = $state(-1);
	let selectedOption = $derived(options.find((opt) => opt.value === value));
	let selectedLabel = $derived(selectedOption?.label ?? placeholder);
	let SelectedIcon = $derived(selectedOption?.icon);
	let rootElement = $state<HTMLDivElement | null>(null);
	let triggerElement = $state<HTMLButtonElement | null>(null);
	let listboxElement = $state<HTMLDivElement | null>(null);
	let sheetElement = $state<HTMLElement | null>(null);
	let handleElement = $state<HTMLButtonElement | null>(null);
	let optionElements = $state<HTMLButtonElement[]>([]);

	const defaultTriggerClass = `${desktopSurfaceTriggerBaseClass} w-full`;
	const defaultMenuClass =
		'border-mist absolute top-full right-0 left-0 z-50 mt-2 transform overflow-hidden rounded-lg border bg-white/95 shadow-dropdown backdrop-blur-sm dark:border-slate-700 dark:bg-slate-900/95';
	const defaultOptionClass =
		'flex min-h-[46px] w-full items-center gap-3 rounded-md px-4 py-3 text-left text-sm font-medium text-ink transition-colors duration-150 hover:bg-coral/6 hover:text-ink dark:text-slate-100 dark:hover:bg-slate-800 dark:hover:text-slate-50';

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

	function selectOption(optionValue: string) {
		value = optionValue;
		highlightedIndex = options.findIndex((option) => option.value === optionValue);
		close();
		onChange?.(optionValue);
	}

	function toggle() {
		if (isOpen) {
			close();
			return;
		}
		open();
	}

	function open() {
		sheet.openSheet(focusTrigger);
		highlightedIndex = getSelectedIndex(options, value);

		if (!isMobile) {
			tick().then(() => {
				focusOption(highlightedIndex);
			});
		}
	}

	function close() {
		sheet.closeSheet(focusTrigger);
	}

	function focusOption(index: number) {
		tick().then(() => {
			optionElements[index]?.focus();
		});
	}

	function handleTriggerKeydown(event: KeyboardEvent) {
		const opensDropdown =
			event.key === 'ArrowDown' || event.key === 'ArrowUp' || event.key === 'Enter' || event.key === ' ';
		if (!opensDropdown) return;

		event.preventDefault();

		if (!isOpen) {
			open();
		}

		if (isMobile) return;

		if (event.key === 'ArrowUp') {
			jumpHighlight(options, options.length - 1, optionElements);
			return;
		}

		if (event.key === 'ArrowDown') {
			jumpHighlight(options, getSelectedIndex(options, value), optionElements);
		}
	}

	function handleDesktopOptionKeydown(event: KeyboardEvent, index: number) {
		if (event.key === 'ArrowDown') {
			event.preventDefault();
			highlightedIndex = moveHighlight(options, highlightedIndex, value, 1, optionElements);
			return;
		}

		if (event.key === 'ArrowUp') {
			event.preventDefault();
			highlightedIndex = moveHighlight(options, highlightedIndex, value, -1, optionElements);
			return;
		}

		if (event.key === 'Home') {
			event.preventDefault();
			highlightedIndex = jumpHighlight(options, 0, optionElements);
			return;
		}

		if (event.key === 'End') {
			event.preventDefault();
			highlightedIndex = jumpHighlight(options, options.length - 1, optionElements);
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

	function handleClickOutside(event: MouseEvent) {
		if (isMobile || !isOpen) return;

		const target = event.target as HTMLElement;
		if (rootElement && !rootElement.contains(target)) {
			close();
		}
	}

	function handleHandlePointerDown(event: PointerEvent) {
		if (!isOpen || !isMobile) return;

		const viewportHeight = innerHeight.current ?? 900;
		const offset = sheet.currentTranslateY || getRestingTranslateY(viewportHeight, sheet.isExpanded);

		sheet.handleDragStart(event, offset, (pointerId) => {
			handleElement?.setPointerCapture(pointerId);
		});
	}

	function handleHandlePointerMove(event: PointerEvent) {
		const result = sheet.handleDragMove(event, {
			onStartDrag() {
				sheet.isDragging = true;
			}
		});
		if (!result) return;

		sheet.currentTranslateY = Math.max(0, result.nextOffset);
		sheet.updateSheetStyles();
	}

	function handleHandlePointerEnd(event: PointerEvent) {
		const result = sheet.handleDragEnd(event, (pointerId) => {
			if (handleElement?.hasPointerCapture(pointerId)) {
				handleElement.releasePointerCapture(pointerId);
			}
		}, focusTrigger);
		if (!result) return;

		if (!result.wasDragging) {
			sheet.resetPointerCapture((pointerId) => {
				if (handleElement?.hasPointerCapture(pointerId)) {
					handleElement.releasePointerCapture(pointerId);
				}
			});
			return;
		}

		result.finishDrag();
		sheet.resetPointerCapture((pointerId) => {
			if (handleElement?.hasPointerCapture(pointerId)) {
				handleElement.releasePointerCapture(pointerId);
			}
		});
	}

	function handleHandleClick(event: MouseEvent) {
		if (sheet.shouldSuppressClick()) {
			event.preventDefault();
			return;
		}

		toggleSheetExpansion();
	}

	function toggleSheetExpansion() {
		sheet.isExpanded = !sheet.isExpanded;
		sheet.isDragging = false;
		const viewportHeight = innerHeight.current ?? 900;
		sheet.currentTranslateY = Math.max(0, getRestingTranslateY(viewportHeight, sheet.isExpanded));
		sheet.updateSheetStyles();
	}

	$effect(() => {
		sheet.sheetStyleManager.mount();

		return () => {
			sheet.clearSheetAnimationTimeout();
			sheet.sheetStyleManager.destroy();
		};
	});

	$effect(() => {
		sheet.updateSheetStyles();
	});

	$effect(() => {
		if (!isOpen || isMobile) return;

		highlightedIndex = getSelectedIndex(options, value);
	});
</script>

<svelte:window onclick={(e: MouseEvent) => handleClickOutside(e)} />

<div bind:this={rootElement} class="relative {className}" data-dropdown-root>
	<DropdownTrigger
		{isOpen}
		{isMobile}
		{triggerId}
		{listboxId}
		{ariaLabel}
		{ariaLabelledby}
		{ariaDescribedby}
		{showIconInTrigger}
		{showLabelInTrigger}
		{selectedLabel}
		selectedIcon={SelectedIcon}
		{triggerClass}
		{defaultTriggerClass}
		bind:triggerElement
		onclick={toggle}
		onkeydown={handleTriggerKeydown}
	/>

	{#if isOpen && !isMobile}
		<div bind:this={listboxElement} class="origin-top animate-dropdown-enter motion-reduce:animate-none" data-dropdown-open>
			<DropdownDesktopList
				{options}
				{value}
				{highlightedIndex}
				{listboxId}
				{menuClass}
				{defaultMenuClass}
				{optionClass}
				{defaultOptionClass}
				{listAriaLabel}
				{ariaLabel}
				{ariaLabelledby}
				{ariaDescribedby}
				{selectedLabel}
				bind:optionElements
				onSelect={selectOption}
				onOptionKeydown={handleDesktopOptionKeydown}
			/>
		</div>
	{/if}
</div>

{#if isMobile}
	<DropdownMobileSheet
		{options}
		{value}
		{selectedLabel}
		{listboxId}
		{sheetInstanceId}
		{mobileSheetTitleId}
		isVisible={sheet.isMobileSheetVisible}
		isOverlayVisible={sheet.isOverlayVisible}
		isDragging={sheet.isDragging}
		isExpanded={sheet.isExpanded}
		currentTranslateY={sheet.currentTranslateY}
		shouldHintMoreContent={shouldHintMoreContent}
		{ariaDescribedby}
		{listAriaLabel}
		{ariaLabel}
		bind:sheetElement
		bind:handleElement
		onClose={close}
		onSelect={selectOption}
		onHandlePointerDown={handleHandlePointerDown}
		onHandlePointerMove={handleHandlePointerMove}
		onHandlePointerEnd={handleHandlePointerEnd}
		onHandleClick={handleHandleClick}
		onToggleSheetExpansion={toggleSheetExpansion}
	/>
{/if}
