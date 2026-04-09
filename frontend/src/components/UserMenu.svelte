<script lang="ts">
	import { tick } from 'svelte';
	import UserMenuDesktopPanel from './user-menu/UserMenuDesktopPanel.svelte';
	import UserMenuMobileSheet from './user-menu/UserMenuMobileSheet.svelte';
	import UserMenuTrigger from './user-menu/UserMenuTrigger.svelte';
	import {
		beginSheetDrag,
		createSheetDragState,
		endSheetDrag,
		resetSheetDrag,
		updateSheetDrag
	} from '../lib/sheet-drag';

	interface Props {
		isLoggedIn: boolean;
		userName?: string;
		userEmail?: string;
		variant?: 'desktop' | 'mobile';
		loginUrl: string;
		logoutUrl: string;
		logoutCsrfToken?: string;
	}

	let {
		isLoggedIn,
		userName = '',
		userEmail = '',
		variant = 'desktop',
		loginUrl,
		logoutUrl,
		logoutCsrfToken = ''
	}: Props = $props();

	let isOpen = $state(false);
	let isMobileSheetVisible = $state(false);
	let isOverlayVisible = $state(false);
	let isClosingSheet = $state(false);
	let isAnimatingIntro = $state(false);
	let isDragging = $state(false);
	let dropdownRef = $state<HTMLDivElement | null>(null);
	let triggerRef = $state<HTMLButtonElement | null>(null);
	let firstActionRef = $state<HTMLButtonElement | null>(null);
	let sheetElement = $state<HTMLElement | null>(null);
	let sheetCloseRef = $state<HTMLButtonElement | null>(null);
	let sheetHandleRef = $state<HTMLButtonElement | null>(null);
	const menuId = `user-menu-${Math.random().toString(36).slice(2, 10)}`;
	const triggerId = `user-menu-trigger-${Math.random().toString(36).slice(2, 10)}`;
	const sheetTitleId = `user-menu-sheet-title-${Math.random().toString(36).slice(2, 10)}`;
	const isMobileVariant = $derived(variant === 'mobile');
	const panelClass = $derived(
		'border-mist absolute right-0 z-50 mt-2 w-[22rem] max-w-[calc(100vw-1.5rem)] transform overflow-hidden rounded-lg border bg-white/95 shadow-dropdown backdrop-blur-sm dark:border-slate-700 dark:bg-slate-900/95'
	);
	let sheetAnimationTimeout: ReturnType<typeof setTimeout> | null = null;
	let currentTranslateY = $state(0);
	const dragState = createSheetDragState();

	function focusTrigger() {
		tick().then(() => {
			triggerRef?.focus();
		});
	}

	function focusFirstAction() {
		tick().then(() => {
			firstActionRef?.focus();
		});
	}

	function openMenu({ focusFirstItem = false } = {}) {
		isOpen = true;

		if (isMobileVariant) {
			currentTranslateY = window.innerHeight + 32;
			isDragging = false;
			isMobileSheetVisible = true;
			isOverlayVisible = false;
			isClosingSheet = false;
			isAnimatingIntro = true;
			clearSheetAnimationTimeout();

			tick().then(() => {
				requestAnimationFrame(() => {
					isOverlayVisible = true;
					currentTranslateY = 0;
					sheetAnimationTimeout = setTimeout(() => {
						isAnimatingIntro = false;
						sheetAnimationTimeout = null;
					}, 280);
				});
			});
		}

		if (focusFirstItem) {
			focusFirstAction();
		}
	}

	function closeMenu({ restoreFocus = false } = {}) {
		if (isMobileVariant && isMobileSheetVisible) {
			startMobileClose({ restoreFocus });
			return;
		}

		finalizeClose({ restoreFocus });
	}

	function clearSheetAnimationTimeout() {
		if (sheetAnimationTimeout) {
			clearTimeout(sheetAnimationTimeout);
			sheetAnimationTimeout = null;
		}
	}

	function finalizeClose({ restoreFocus = false } = {}) {
		isOpen = false;
		isMobileSheetVisible = false;
		isOverlayVisible = false;
		isClosingSheet = false;
		isAnimatingIntro = false;
		isDragging = false;
		currentTranslateY = 0;
		resetDragState();
		clearSheetAnimationTimeout();
		if (restoreFocus) {
			focusTrigger();
		}
	}

	function startMobileClose({ restoreFocus = false } = {}) {
		if (isClosingSheet) return;

		isOpen = false;
		isClosingSheet = true;
		isAnimatingIntro = false;
		isDragging = false;
		isOverlayVisible = false;
		currentTranslateY = window.innerHeight + 32;
		resetDragState();
		clearSheetAnimationTimeout();
		sheetAnimationTimeout = setTimeout(() => {
			finalizeClose({ restoreFocus });
		}, 220);
	}

	function resetDragState() {
		resetSheetDrag(dragState, {
			releasePointerCaptureIfNeeded(pointerId) {
				if (sheetHandleRef?.hasPointerCapture(pointerId)) {
					sheetHandleRef.releasePointerCapture(pointerId);
				}
			}
		});
	}

	function handleSheetHandlePointerDown(event: PointerEvent) {
		if (!isMobileVariant || !isOpen) return;

		beginSheetDrag(event, dragState, {
			currentOffset: currentTranslateY,
			setPointerCapture(pointerId) {
				sheetHandleRef?.setPointerCapture(pointerId);
			}
		});
	}

	function handleSheetHandlePointerMove(event: PointerEvent) {
		const result = updateSheetDrag(event, dragState, {
			onStartDrag() {
				isDragging = true;
			},
			resolveNextOffset({ startOffset, dragDeltaY }) {
				const nextOffset = Math.max(0, startOffset + dragDeltaY);
				const resistance = nextOffset > 0 ? nextOffset * 0.08 : 0;
				return Math.max(0, nextOffset - resistance);
			}
		});
		if (!result) return;

		currentTranslateY = result.nextOffset;
	}

	function handleSheetHandlePointerEnd(event: PointerEvent) {
		const result = endSheetDrag(event, dragState, {
			releasePointerCapture(pointerId) {
				if (sheetHandleRef?.hasPointerCapture(pointerId)) {
					sheetHandleRef.releasePointerCapture(pointerId);
				}
			}
		});
		if (!result) return;

		if (!result.wasDragging) {
			resetDragState();
			return;
		}

		const shouldClose = currentTranslateY > 120 && result.dragDeltaY > 0;
		isDragging = false;

		if (shouldClose) {
			startMobileClose({ restoreFocus: true });
			return;
		}

		currentTranslateY = 0;
		resetDragState();
	}

	function handleSheetHandleClick(event: MouseEvent) {
		if (dragState.suppressHandleClick) {
			dragState.suppressHandleClick = false;
			event.preventDefault();
		}
	}

	function toggleMenu() {
		if (isOpen) {
			closeMenu();
			return;
		}

		openMenu();
	}

	function handleDocumentKeydown(event: KeyboardEvent) {
		if (event.key === 'Escape') {
			closeMenu({ restoreFocus: true });
		}
	}

	function handleDocumentClick(event: MouseEvent) {
		if (isMobileVariant) return;
		if (!isOpen || !dropdownRef) return;

		if (!dropdownRef.contains(event.target as Node)) {
			closeMenu();
		}
	}

	function handleTriggerKeydown(event: KeyboardEvent) {
		if (event.key === 'ArrowDown') {
			event.preventDefault();
			if (!isOpen) {
				openMenu({ focusFirstItem: true });
				return;
			}

			focusFirstAction();
		}
	}

</script>

<svelte:document onkeydown={handleDocumentKeydown} onclick={handleDocumentClick} />

<div class="relative" bind:this={dropdownRef}>
	<UserMenuTrigger
		{isLoggedIn}
		{isOpen}
		{isMobileVariant}
		{userName}
		{userEmail}
		{triggerId}
		menuId={menuId}
		bind:triggerRef
		onclick={toggleMenu}
		onkeydown={handleTriggerKeydown}
	/>

	{#if isOpen && !isMobileVariant}
		<UserMenuDesktopPanel
			{menuId}
			{triggerId}
			panelClass={panelClass}
			{isLoggedIn}
			{userName}
			{userEmail}
			{loginUrl}
			{logoutUrl}
			{logoutCsrfToken}
			bind:firstActionRef
			onNavigate={() => closeMenu()}
		/>
	{/if}
</div>

<UserMenuMobileSheet
	isVisible={isMobileVariant && isMobileSheetVisible}
	{isOverlayVisible}
	{isClosingSheet}
	{isAnimatingIntro}
	{isDragging}
	{currentTranslateY}
	{menuId}
	{sheetTitleId}
	bind:sheetElement
	bind:sheetCloseRef
	bind:sheetHandleRef
	bind:firstActionRef
	{isLoggedIn}
	{userName}
	{userEmail}
	{loginUrl}
	{logoutUrl}
	{logoutCsrfToken}
	onClose={() => closeMenu({ restoreFocus: true })}
	onNavigate={() => closeMenu()}
	onHandlePointerDown={handleSheetHandlePointerDown}
	onHandlePointerMove={handleSheetHandlePointerMove}
	onHandlePointerEnd={handleSheetHandlePointerEnd}
	onHandleClick={handleSheetHandleClick}
/>
