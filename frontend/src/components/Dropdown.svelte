<script lang="ts">
	import { innerHeight, innerWidth } from 'svelte/reactivity/window';
	import { tick } from 'svelte';
	import DropdownDesktopList from './dropdown/DropdownDesktopList.svelte';
	import DropdownMobileSheet from './dropdown/DropdownMobileSheet.svelte';
	import DropdownTrigger from './dropdown/DropdownTrigger.svelte';
	import type { DropdownOption } from '../lib/dropdown';
	import { createSheetStyleManager } from '../lib/mobile-sheet-styles';
	import { desktopSurfaceTriggerBaseClass } from '../lib/ui-classes';
	import {
		beginSheetDrag,
		createSheetDragState,
		endSheetDrag,
		resetSheetDrag,
		updateSheetDrag
	} from '../lib/sheet-drag';

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
		mobileBreakpoint = 768,
		onChange
	}: Props = $props();

	let isOpen = $state(false);
	let isExpanded = $state(false);
	let isDragging = $state(false);
	let highlightedIndex = $state(-1);
	let selectedOption = $derived(options.find((opt) => opt.value === value));
	let selectedLabel = $derived(selectedOption?.label ?? placeholder);
	let SelectedIcon = $derived(selectedOption?.icon);
	let viewportWidth = $derived(innerWidth.current ?? 1024);
	let viewportHeight = $derived(innerHeight.current ?? 900);
	let isMobile = $derived(viewportWidth < mobileBreakpoint);
	let rootElement = $state<HTMLDivElement | null>(null);
	let triggerElement = $state<HTMLButtonElement | null>(null);
	let listboxElement = $state<HTMLDivElement | null>(null);
	let sheetElement = $state<HTMLElement | null>(null);
	let handleElement = $state<HTMLButtonElement | null>(null);
	let optionElements = $state<HTMLButtonElement[]>([]);
	const sheetInstanceId = `dropdown-sheet-${Math.random().toString(36).slice(2, 10)}`;
	const listboxId = `dropdown-listbox-${Math.random().toString(36).slice(2, 10)}`;
	const triggerId = `dropdown-trigger-${Math.random().toString(36).slice(2, 10)}`;
	const mobileSheetTitleId = `dropdown-sheet-title-${Math.random().toString(36).slice(2, 10)}`;
	let currentTranslateY = $state(0);
	let shouldHintMoreContent = $derived(!isExpanded && options.length > 6);
	const dragState = createSheetDragState();
	const sheetStyleManager = createSheetStyleManager(sheetInstanceId);
	let isMobileSheetVisible = $state(false);
	let isOverlayVisible = $state(false);
	let isAnimatingIntro = $state(false);
	let isClosingSheet = $state(false);
	let sheetAnimationTimeout: ReturnType<typeof setTimeout> | null = null;

	const defaultTriggerClass = `${desktopSurfaceTriggerBaseClass} w-full`;
	const defaultMenuClass =
		'border-mist absolute top-full right-0 left-0 z-50 mt-2 transform overflow-hidden rounded-lg border bg-white/95 shadow-dropdown backdrop-blur-sm dark:border-slate-700 dark:bg-slate-900/95';
	const defaultOptionClass =
		'flex min-h-[46px] w-full items-center gap-3 rounded-md px-4 py-3 text-left text-sm font-medium text-ink transition-colors duration-150 hover:bg-coral/6 hover:text-ink dark:text-slate-100 dark:hover:bg-slate-800 dark:hover:text-slate-50';

	function getSelectedIndex() {
		const selectedIndex = options.findIndex((option) => option.value === value);
		return selectedIndex >= 0 ? selectedIndex : 0;
	}

	function focusDesktopOption(index: number) {
		tick().then(() => {
			optionElements[index]?.focus();
		});
	}

	function clearSheetAnimationTimeout() {
		if (sheetAnimationTimeout) {
			clearTimeout(sheetAnimationTimeout);
			sheetAnimationTimeout = null;
		}
	}

	function openDropdown({ focusSelectedOption = false }: { focusSelectedOption?: boolean } = {}) {
		isOpen = true;
		isExpanded = false;
		highlightedIndex = getSelectedIndex();

		if (isMobile) {
			currentTranslateY = getMaximumSheetHeight() + 32;
			isMobileSheetVisible = true;
			isOverlayVisible = false;
			isAnimatingIntro = true;
			isClosingSheet = false;
			updateSheetStyles();

			tick().then(() => {
				requestAnimationFrame(() => {
					isOverlayVisible = true;
					currentTranslateY = getCollapsedOffset();
					updateSheetStyles();
					clearSheetAnimationTimeout();
					sheetAnimationTimeout = setTimeout(() => {
						isAnimatingIntro = false;
						sheetAnimationTimeout = null;
					}, 280);
				});
			});
		} else {
			currentTranslateY = getCollapsedOffset();
		}

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

	function resetDragState() {
		resetSheetDrag(dragState, {
			releasePointerCaptureIfNeeded(pointerId) {
				if (handleElement?.hasPointerCapture(pointerId)) {
					handleElement.releasePointerCapture(pointerId);
				}
			}
		});
	}

	function finalizeClose({ restoreFocus = isMobile } = {}) {
		isOpen = false;
		isMobileSheetVisible = false;
		isOverlayVisible = false;
		isExpanded = false;
		isDragging = false;
		isAnimatingIntro = false;
		isClosingSheet = false;
		highlightedIndex = -1;
		currentTranslateY = 0;
		clearSheetAnimationTimeout();
		resetDragState();
		if (restoreFocus) {
			focusTrigger();
		}
	}

	function startCloseAnimation({
		restoreFocus = isMobile
	}: {
		restoreFocus?: boolean;
	} = {}) {
		if (!isMobile || !isMobileSheetVisible) {
			finalizeClose({ restoreFocus });
			return;
		}

		isDragging = false;
		isAnimatingIntro = false;
		isClosingSheet = true;
		isOverlayVisible = false;
		clearSheetAnimationTimeout();

		requestAnimationFrame(() => {
			currentTranslateY = getMaximumSheetHeight() + 32;
			updateSheetStyles();
			sheetAnimationTimeout = setTimeout(() => {
				finalizeClose({ restoreFocus });
			}, 220);
		});
	}

	function close({ restoreFocus = isMobile } = {}) {
		if (isClosingSheet) return;

		startCloseAnimation({ restoreFocus });
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
		const opensDropdown =
			event.key === 'ArrowDown' || event.key === 'ArrowUp' || event.key === 'Enter' || event.key === ' ';
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

	function handleClickOutside(event: MouseEvent) {
		if (isMobile || !isOpen) return;

		const target = event.target as HTMLElement;
		if (rootElement && !rootElement.contains(target)) {
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
	}

	function updateSheetStyles() {
		if ((!isOpen && !isMobileSheetVisible) || !isMobile) {
			sheetStyleManager.clear();
			return;
		}

		const maxSheetHeight = getMaximumSheetHeight();
		const scrollHeight = Math.max(maxSheetHeight - 92, 220);

		sheetStyleManager.update({
			isActive: true,
			height: maxSheetHeight,
			maxHeight: `calc(100dvh - ${getExpandedTopOffset()}px)`,
			translateY:
				isDragging || isAnimatingIntro || isClosingSheet ? currentTranslateY : getRestingTranslateY(),
			scrollHeight,
			allowScroll: isExpanded
		});
	}

	function finishDrag() {
		const finalOffset = currentTranslateY;
		const collapsedOffset = getCollapsedOffset();
		const snapThreshold = 80;
		const closeThreshold = Math.max(collapsedOffset + 140, viewportHeight * 0.72);
		const wantsClose = finalOffset > closeThreshold && dragState.dragDeltaY > 0;
		const wantsExpand = dragState.dragDeltaY < -snapThreshold || finalOffset < collapsedOffset * 0.5;
		const wantsCollapse = dragState.dragDeltaY > snapThreshold || finalOffset >= collapsedOffset * 0.5;

		if (wantsClose) {
			startCloseAnimation();
			return;
		}

		if (wantsExpand) {
			isExpanded = true;
		} else if (wantsCollapse) {
			isExpanded = false;
		}

		isDragging = false;
		applySheetTranslateY(getRestingTranslateY());
	}

	function handleHandlePointerMove(event: PointerEvent) {
		const result = updateSheetDrag(event, dragState, {
			onStartDrag() {
				isDragging = true;
			},
			resolveNextOffset({ startOffset, dragDeltaY }) {
				const collapsedOffset = getCollapsedOffset();
				const nextOffset = Math.max(0, startOffset + dragDeltaY);
				const resistance =
					nextOffset > collapsedOffset ? (nextOffset - collapsedOffset) * 0.35 : 0;

				return nextOffset - resistance;
			}
		});
		if (!result) return;

		applySheetTranslateY(result.nextOffset);
	}

	function handleHandlePointerEnd(event: PointerEvent) {
		const result = endSheetDrag(event, dragState, {
			releasePointerCapture(pointerId) {
				if (handleElement?.hasPointerCapture(pointerId)) {
					handleElement.releasePointerCapture(pointerId);
				}
			}
		});
		if (!result) return;

		if (!result.wasDragging) {
			resetDragState();
			return;
		}

		finishDrag();
		resetDragState();
	}

	function handleHandlePointerDown(event: PointerEvent) {
		if (!isOpen || !isMobile) return;

		beginSheetDrag(event, dragState, {
			currentOffset: currentTranslateY || getRestingTranslateY(),
			setPointerCapture(pointerId) {
				handleElement?.setPointerCapture(pointerId);
			}
		});
	}

	function toggleSheetExpansion() {
		isExpanded = !isExpanded;
		isDragging = false;
		applySheetTranslateY(getRestingTranslateY());
	}

	function handleHandleClick(event: MouseEvent) {
		if (dragState.suppressHandleClick) {
			dragState.suppressHandleClick = false;
			event.preventDefault();
			return;
		}

		toggleSheetExpansion();
	}

	$effect(() => {
		sheetStyleManager.mount();
		updateSheetStyles();

		return () => {
			clearSheetAnimationTimeout();
			sheetStyleManager.destroy();
		};
	});

	$effect(() => {
		updateSheetStyles();
	});

	$effect(() => {
		if (!isOpen || isMobile) return;

		highlightedIndex = getSelectedIndex();
	});
</script>

<svelte:window onclick={handleClickOutside} />

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
		<div bind:this={listboxElement}>
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
		isVisible={isMobileSheetVisible}
		{isOverlayVisible}
		{isDragging}
		{isExpanded}
		{shouldHintMoreContent}
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
