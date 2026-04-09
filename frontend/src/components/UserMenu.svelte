<script lang="ts">
  import { tick } from "svelte";
  import UserMenuDesktopPanel from "./user-menu/UserMenuDesktopPanel.svelte";
  import UserMenuMobileSheet from "./user-menu/UserMenuMobileSheet.svelte";
  import UserMenuTrigger from "./user-menu/UserMenuTrigger.svelte";
  import { createMobileSheetManager } from "../lib/use-mobile-sheet.svelte";

  interface Props {
    isLoggedIn: boolean;
    userName?: string;
    userEmail?: string;
    variant?: "desktop" | "mobile";
    loginUrl: string;
    logoutUrl: string;
    logoutCsrfToken?: string;
  }

  let {
    isLoggedIn,
    userName = "",
    userEmail = "",
    variant = "desktop",
    loginUrl,
    logoutUrl,
    logoutCsrfToken = "",
  }: Props = $props();

  const isMobileVariant = $derived(variant === "mobile");

  const sheet = createMobileSheetManager({
    sheetDataId: `user-menu-sheet-${Math.random().toString(36).slice(2, 10)}`,
    variant: "user-menu",
    getViewportHeight: () => window.innerHeight,
    getViewportWidth: () => window.innerWidth,
  });

  let dropdownRef = $state<HTMLDivElement | null>(null);
  let triggerRef = $state<HTMLButtonElement | null>(null);
  let firstActionRef = $state<HTMLAnchorElement | HTMLButtonElement | null>(
    null,
  );
  let sheetCloseRef = $state<HTMLButtonElement | null>(null);
  let sheetHandleRef = $state<HTMLButtonElement | null>(null);
  let sheetElement = $state<HTMLElement | null>(null);
  const menuId = `user-menu-${Math.random().toString(36).slice(2, 10)}`;
  const triggerId = `user-menu-trigger-${Math.random().toString(36).slice(2, 10)}`;
  const sheetTitleId = `user-menu-sheet-title-${Math.random().toString(36).slice(2, 10)}`;

  const panelClass = $derived(
    "border-mist absolute right-0 z-50 mt-2 w-[22rem] max-w-[calc(100vw-1.5rem)] transform overflow-hidden rounded-lg border bg-white/95 shadow-dropdown backdrop-blur-sm dark:border-slate-700 dark:bg-slate-900/95",
  );

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
    sheet.openSheet(focusTrigger);

    if (focusFirstItem) {
      focusFirstAction();
    }
  }

  function closeMenu({ restoreFocus = false } = {}) {
    sheet.closeSheet(restoreFocus ? focusTrigger : () => {});
  }

  function toggleMenu() {
    if (sheet.isOpen) {
      closeMenu();
      return;
    }

    openMenu();
  }

  function handleDocumentKeydown(event: KeyboardEvent) {
    if (event.key === "Escape") {
      closeMenu({ restoreFocus: true });
    }
  }

  function handleDocumentClick(event: MouseEvent) {
    if (isMobileVariant) return;
    if (!sheet.isOpen || !dropdownRef) return;

    if (!dropdownRef.contains(event.target as Node)) {
      closeMenu();
    }
  }

  function handleSheetHandlePointerDown(event: PointerEvent) {
    if (!isMobileVariant || !sheet.isOpen) return;

    sheet.handleDragStart(event, sheet.currentTranslateY, (pointerId) => {
      sheetHandleRef?.setPointerCapture(pointerId);
    });
  }

  function handleSheetHandlePointerMove(event: PointerEvent) {
    const result = sheet.handleDragMove(event, {
      onStartDrag() {
        sheet.isDragging = true;
      },
    });
    if (!result) return;

    sheet.currentTranslateY = result.nextOffset;
  }

  function handleSheetHandlePointerEnd(event: PointerEvent) {
    const result = sheet.handleDragEnd(
      event,
      (pointerId) => {
        if (sheetHandleRef?.hasPointerCapture(pointerId)) {
          sheetHandleRef.releasePointerCapture(pointerId);
        }
      },
      focusTrigger,
    );
    if (!result) return;

    if (!result.wasDragging) {
      sheet.resetPointerCapture((pointerId) => {
        if (sheetHandleRef?.hasPointerCapture(pointerId)) {
          sheetHandleRef.releasePointerCapture(pointerId);
        }
      });
      return;
    }

    result.finishDrag();
    sheet.resetPointerCapture((pointerId) => {
      if (sheetHandleRef?.hasPointerCapture(pointerId)) {
        sheetHandleRef.releasePointerCapture(pointerId);
      }
    });
  }

  function handleSheetHandleClick(event: MouseEvent) {
    if (sheet.shouldSuppressClick()) {
      event.preventDefault();
    }
  }

  function handleTriggerKeydown(event: KeyboardEvent) {
    if (event.key === "ArrowDown") {
      event.preventDefault();
      if (!sheet.isOpen) {
        openMenu({ focusFirstItem: true });
        return;
      }

      focusFirstAction();
    }
  }
</script>

<svelte:document
  onkeydown={handleDocumentKeydown}
  onclick={(e: MouseEvent) => handleDocumentClick(e)}
/>

<div class="relative" bind:this={dropdownRef}>
  <UserMenuTrigger
    {isLoggedIn}
    isOpen={sheet.isOpen}
    {isMobileVariant}
    {userName}
    {userEmail}
    {triggerId}
    {menuId}
    bind:triggerRef
    onclick={toggleMenu}
    onkeydown={handleTriggerKeydown}
  />

  {#if sheet.isOpen && !isMobileVariant}
    <div class="origin-top animate-dropdown-enter motion-reduce:animate-none">
      <UserMenuDesktopPanel
        {menuId}
        {triggerId}
        {panelClass}
        {isLoggedIn}
        {userName}
        {userEmail}
        {loginUrl}
        {logoutUrl}
        {logoutCsrfToken}
        bind:firstActionRef
        onNavigate={() => closeMenu()}
      />
    </div>
  {/if}
</div>

<UserMenuMobileSheet
  isVisible={isMobileVariant && sheet.isMobileSheetVisible}
  isOverlayVisible={sheet.isOverlayVisible}
  isClosingSheet={sheet.isClosingSheet}
  isAnimatingIntro={sheet.isAnimatingIntro}
  isDragging={sheet.isDragging}
  currentTranslateY={sheet.currentTranslateY}
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
