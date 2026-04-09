import { tick } from "svelte";
import { createSheetDragState } from "./sheet-drag";
import { createSheetStyleManager } from "./mobile-sheet-styles";
import {
  SHEET_ANIMATION_DURATION_MS,
  SHEET_INTRO_DURATION_MS,
  SHEET_INITIAL_OFFSET_PX,
  SHEET_SNAP_THRESHOLD_PX,
  SHEET_CLOSE_THRESHOLD_PX,
  DROPDOWN_DRAG_RESISTANCE,
  USER_MENU_DRAG_RESISTANCE,
  USER_MENU_CLOSE_THRESHOLD_PX,
} from "./sheet-constants";
import {
  getMaximumSheetHeight,
  getCollapsedOffset,
  getRestingTranslateY,
  getScrollContentHeight,
} from "./sheet-geometry";

type SheetVariant = "dropdown" | "user-menu";

export interface MobileSheetState {
  isOpen: boolean;
  isExpanded: boolean;
  isDragging: boolean;
  isMobileSheetVisible: boolean;
  isOverlayVisible: boolean;
  isAnimatingIntro: boolean;
  isClosingSheet: boolean;
  currentTranslateY: number;
}

export interface MobileSheetCallbacks {
  onOpen?: () => void;
  onClose?: () => void;
  getViewportHeight: () => number;
  getViewportWidth: () => number;
  sheetDataId: string;
  variant: SheetVariant;
}

function getDragResistance(variant: SheetVariant) {
  return variant === "user-menu"
    ? USER_MENU_DRAG_RESISTANCE
    : DROPDOWN_DRAG_RESISTANCE;
}

function getCloseThreshold(variant: SheetVariant) {
  return variant === "user-menu"
    ? USER_MENU_CLOSE_THRESHOLD_PX
    : SHEET_CLOSE_THRESHOLD_PX;
}

function getInitialExpandedState(variant: SheetVariant) {
  return variant === "user-menu";
}

function getHiddenTranslateY(variant: SheetVariant, viewportHeight: number) {
  return variant === "user-menu"
    ? viewportHeight + SHEET_INITIAL_OFFSET_PX
    : getMaximumSheetHeight(viewportHeight) + SHEET_INITIAL_OFFSET_PX;
}

export function createMobileSheetManager(callbacks: MobileSheetCallbacks) {
  const { variant, sheetDataId } = callbacks;

  let isOpen = $state(false);
  let isExpanded = $state(false);
  let isDragging = $state(false);
  let isMobileSheetVisible = $state(false);
  let isOverlayVisible = $state(false);
  let isAnimatingIntro = $state(false);
  let isClosingSheet = $state(false);
  let currentTranslateY = $state(0);
  let sheetAnimationTimeout: ReturnType<typeof setTimeout> | null = null;

  const dragState = createSheetDragState();
  const sheetStyleManager = createSheetStyleManager(sheetDataId);

  function clearSheetAnimationTimeout() {
    if (sheetAnimationTimeout) {
      clearTimeout(sheetAnimationTimeout);
      sheetAnimationTimeout = null;
    }
  }

  function finalizeClose(restoreFocus: boolean, focusTrigger: () => void) {
    isOpen = false;
    isMobileSheetVisible = false;
    isOverlayVisible = false;
    isExpanded = false;
    isDragging = false;
    isAnimatingIntro = false;
    isClosingSheet = false;
    currentTranslateY = 0;
    clearSheetAnimationTimeout();
    resetDragState();
    if (restoreFocus) {
      focusTrigger();
    }
  }

  function startCloseAnimation(focusTrigger: () => void) {
    const viewportHeight = callbacks.getViewportHeight();

    if (variant === "user-menu") {
      if (isClosingSheet) return;
      isOpen = false;
      isClosingSheet = true;
      isAnimatingIntro = false;
      isDragging = false;
      isOverlayVisible = false;
      currentTranslateY = getHiddenTranslateY(variant, viewportHeight);
      resetDragState();
      clearSheetAnimationTimeout();
      sheetAnimationTimeout = setTimeout(() => {
        finalizeClose(true, focusTrigger);
      }, SHEET_ANIMATION_DURATION_MS);
      return;
    }

    if (!isMobileSheetVisible) {
      finalizeClose(false, focusTrigger);
      return;
    }

    isOpen = false;
    isDragging = false;
    isAnimatingIntro = false;
    isClosingSheet = true;
    isOverlayVisible = false;
    clearSheetAnimationTimeout();

    requestAnimationFrame(() => {
      currentTranslateY = getHiddenTranslateY(variant, viewportHeight);
      updateSheetStyles();
      sheetAnimationTimeout = setTimeout(() => {
        finalizeClose(true, focusTrigger);
      }, SHEET_ANIMATION_DURATION_MS);
    });
  }

  function resetDragState() {
    dragState.activePointerId = null;
    dragState.dragDeltaY = 0;
    dragState.dragStartOffset = 0;
    dragState.dragStartY = 0;
    dragState.isDragging = false;
    dragState.suppressHandleClick = false;
  }

  function updateSheetStyles() {
    const viewportHeight = callbacks.getViewportHeight();

    if (
      (!isOpen && !isMobileSheetVisible) ||
      callbacks.getViewportWidth() >= 768
    ) {
      sheetStyleManager.clear();
      return;
    }

    const maxSheetHeight = getMaximumSheetHeight(viewportHeight);
    const scrollHeight = getScrollContentHeight(viewportHeight);

    sheetStyleManager.update({
      isActive: true,
      height: maxSheetHeight,
      maxHeight: `calc(100dvh - 8px)`,
      translateY:
        isDragging || isAnimatingIntro || isClosingSheet
          ? currentTranslateY
          : getRestingTranslateY(viewportHeight, isExpanded),
      scrollHeight,
      allowScroll: isExpanded,
    });
  }

  function openSheet(focusTrigger?: () => void) {
    const viewportHeight = callbacks.getViewportHeight();
    const initiallyExpanded = getInitialExpandedState(variant);

    isOpen = true;
    isExpanded = initiallyExpanded;

    currentTranslateY = getHiddenTranslateY(variant, viewportHeight);
    isMobileSheetVisible = true;
    isOverlayVisible = false;
    isAnimatingIntro = true;
    isClosingSheet = false;

    tick().then(() => {
      requestAnimationFrame(() => {
        isOverlayVisible = true;
        currentTranslateY = getRestingTranslateY(
          viewportHeight,
          initiallyExpanded,
        );
        updateSheetStyles();
        clearSheetAnimationTimeout();
        sheetAnimationTimeout = setTimeout(() => {
          isAnimatingIntro = false;
          sheetAnimationTimeout = null;
        }, SHEET_INTRO_DURATION_MS);
      });
    });

    callbacks.onOpen?.();
  }

  function closeSheet(focusTrigger: () => void) {
    startCloseAnimation(focusTrigger);
    callbacks.onClose?.();
  }

  function handleDragStart(
    event: PointerEvent,
    currentOffset: number,
    setPointerCapture: (id: number) => void,
  ): boolean {
    if (!event.isPrimary) return false;
    if (event.pointerType === "mouse" && event.button !== 0) return false;

    dragState.activePointerId = event.pointerId;
    dragState.dragStartY = event.clientY;
    dragState.dragStartOffset = currentOffset;
    dragState.dragDeltaY = 0;
    dragState.suppressHandleClick = false;
    dragState.isDragging = false;
    setPointerCapture(event.pointerId);
    return true;
  }

  function handleDragMove(
    event: PointerEvent,
    options: { onStartDrag?: () => void },
  ) {
    if (event.pointerId !== dragState.activePointerId) return null;

    dragState.dragDeltaY = event.clientY - dragState.dragStartY;
    if (
      !dragState.isDragging &&
      Math.abs(dragState.dragDeltaY) < SHEET_SNAP_THRESHOLD_PX
    )
      return null;

    if (!dragState.isDragging) {
      dragState.isDragging = true;
      dragState.suppressHandleClick = true;
      options.onStartDrag?.();
    }

    event.preventDefault();

    const viewportHeight = callbacks.getViewportHeight();
    const resistance = getDragResistance(variant);
    const collapsedOffset = getCollapsedOffset(viewportHeight);

    let nextOffset = Math.max(
      0,
      dragState.dragStartOffset + dragState.dragDeltaY,
    );

    if (variant === "dropdown" && nextOffset > collapsedOffset) {
      const excess = nextOffset - collapsedOffset;
      nextOffset = collapsedOffset + excess * (1 - resistance);
    } else if (variant === "user-menu" && nextOffset > 0) {
      nextOffset = Math.max(0, nextOffset - nextOffset * resistance);
    }

    return { dragDeltaY: dragState.dragDeltaY, nextOffset };
  }

  function handleDragEnd(
    event: PointerEvent,
    releasePointerCapture: (id: number) => void,
    focusTrigger: () => void,
  ) {
    if (event.pointerId !== dragState.activePointerId) return null;

    releasePointerCapture(event.pointerId);

    const closeThreshold = getCloseThreshold(variant);
    let shouldClose = false;

    if (variant === "user-menu") {
      shouldClose =
        currentTranslateY > closeThreshold && dragState.dragDeltaY > 0;
    } else {
      const viewportHeight = callbacks.getViewportHeight();
      const collapsedOffset = getCollapsedOffset(viewportHeight);
      const absoluteCloseThreshold = Math.max(
        collapsedOffset + closeThreshold,
        viewportHeight * 0.72,
      );
      shouldClose =
        dragState.dragDeltaY > 0 && currentTranslateY > absoluteCloseThreshold;
    }

    return {
      wasDragging: dragState.isDragging,
      dragDeltaY: dragState.dragDeltaY,
      shouldClose,
      finishDrag: () => {
        if (variant === "dropdown") {
          const viewportHeight = callbacks.getViewportHeight();
          const collapsedOffset = getCollapsedOffset(viewportHeight);
          const wantsExpand =
            dragState.dragDeltaY < -SHEET_SNAP_THRESHOLD_PX ||
            currentTranslateY < collapsedOffset * 0.5;
          const wantsCollapse =
            dragState.dragDeltaY > SHEET_SNAP_THRESHOLD_PX ||
            currentTranslateY >= collapsedOffset * 0.5;

          if (shouldClose) {
            closeSheet(focusTrigger);
            return;
          }
          if (wantsExpand) {
            isExpanded = true;
          } else if (wantsCollapse) {
            isExpanded = false;
          }
          isDragging = false;
          currentTranslateY = Math.max(
            0,
            getRestingTranslateY(viewportHeight, isExpanded),
          );
        } else {
          isDragging = false;
          if (shouldClose) {
            closeSheet(focusTrigger);
            return;
          }
          currentTranslateY = 0;
        }
      },
    };
  }

  function resetPointerCapture(releasePointerCapture: (id: number) => void) {
    if (dragState.activePointerId !== null) {
      releasePointerCapture(dragState.activePointerId);
    }
    dragState.dragDeltaY = 0;
    dragState.dragStartOffset = 0;
    dragState.dragStartY = 0;
    dragState.isDragging = false;
    dragState.suppressHandleClick = false;
    dragState.activePointerId = null;
  }

  return {
    get isOpen() {
      return isOpen;
    },
    set isOpen(v: boolean) {
      isOpen = v;
    },
    get isExpanded() {
      return isExpanded;
    },
    set isExpanded(v: boolean) {
      isExpanded = v;
    },
    get isDragging() {
      return isDragging;
    },
    set isDragging(v: boolean) {
      isDragging = v;
    },
    get isMobileSheetVisible() {
      return isMobileSheetVisible;
    },
    set isMobileSheetVisible(v: boolean) {
      isMobileSheetVisible = v;
    },
    get isOverlayVisible() {
      return isOverlayVisible;
    },
    set isOverlayVisible(v: boolean) {
      isOverlayVisible = v;
    },
    get isAnimatingIntro() {
      return isAnimatingIntro;
    },
    set isAnimatingIntro(v: boolean) {
      isAnimatingIntro = v;
    },
    get isClosingSheet() {
      return isClosingSheet;
    },
    set isClosingSheet(v: boolean) {
      isClosingSheet = v;
    },
    get currentTranslateY() {
      return currentTranslateY;
    },
    set currentTranslateY(v: number) {
      currentTranslateY = v;
    },

    dragState,
    sheetStyleManager,
    clearSheetAnimationTimeout,
    finalizeClose,
    openSheet,
    closeSheet,
    updateSheetStyles,
    handleDragStart,
    handleDragMove,
    handleDragEnd,
    resetPointerCapture,
    shouldSuppressClick: () => {
      if (dragState.suppressHandleClick) {
        dragState.suppressHandleClick = false;
        return true;
      }
      return false;
    },
  };
}
