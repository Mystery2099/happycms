export type SheetDragState = {
	activePointerId: number | null;
	dragStartY: number;
	dragStartOffset: number;
	dragDeltaY: number;
	suppressHandleClick: boolean;
	isDragging: boolean;
};

/**
 * Keep transient pointer drag state outside component markup so mobile sheet
 * interactions can be coordinated across pointerdown, move, and release events.
 */
export function createSheetDragState(): SheetDragState {
	return {
		activePointerId: null,
		dragStartY: 0,
		dragStartOffset: 0,
		dragDeltaY: 0,
		suppressHandleClick: false,
		isDragging: false
	};
}

/**
 * Claim the active pointer and snapshot the starting geometry for a potential
 * drag gesture. Non-primary pointers and non-left mouse buttons are ignored.
 */
export function beginSheetDrag(
	event: PointerEvent,
	state: SheetDragState,
	{
		currentOffset,
		setPointerCapture
	}: {
		currentOffset: number;
		setPointerCapture: (pointerId: number) => void;
	}
) {
	if (!event.isPrimary) return false;
	if (event.pointerType === 'mouse' && event.button !== 0) return false;

	state.activePointerId = event.pointerId;
	state.dragStartY = event.clientY;
	state.dragStartOffset = currentOffset;
	state.dragDeltaY = 0;
	state.suppressHandleClick = false;
	state.isDragging = false;
	setPointerCapture(event.pointerId);
	return true;
}

/**
 * Convert pointer movement into the next sheet offset once the gesture crosses a
 * small threshold, preventing accidental drags from taps on the handle.
 */
export function updateSheetDrag(
	event: PointerEvent,
	state: SheetDragState,
	{
		onStartDrag,
		resolveNextOffset
	}: {
		onStartDrag?: () => void;
		resolveNextOffset: (args: { startOffset: number; dragDeltaY: number }) => number;
	}
) {
	if (event.pointerId !== state.activePointerId) return null;

	state.dragDeltaY = event.clientY - state.dragStartY;
	if (!state.isDragging && Math.abs(state.dragDeltaY) < 6) return null;

	if (!state.isDragging) {
		state.isDragging = true;
		state.suppressHandleClick = true;
		onStartDrag?.();
	}

	event.preventDefault();

	return {
		dragDeltaY: state.dragDeltaY,
		nextOffset: resolveNextOffset({
			startOffset: state.dragStartOffset,
			dragDeltaY: state.dragDeltaY
		})
	};
}

/**
 * Finish the active drag gesture and report whether a real drag occurred so the
 * caller can decide between snapping, closing, or treating it as a tap.
 */
export function endSheetDrag(
	event: PointerEvent,
	state: SheetDragState,
	{
		releasePointerCapture
	}: {
		releasePointerCapture: (pointerId: number) => void;
	}
) {
	if (event.pointerId !== state.activePointerId) return null;

	releasePointerCapture(event.pointerId);

	return {
		wasDragging: state.isDragging,
		dragDeltaY: state.dragDeltaY
	};
}

/**
 * Clear all drag bookkeeping and release any captured pointer after interruptions
 * such as closing the sheet or tearing down the component.
 */
export function resetSheetDrag(
	state: SheetDragState,
	{
		releasePointerCaptureIfNeeded
	}: {
		releasePointerCaptureIfNeeded: (pointerId: number) => void;
	}
) {
	const pointerId = state.activePointerId;
	state.dragDeltaY = 0;
	state.dragStartOffset = 0;
	state.dragStartY = 0;
	state.isDragging = false;
	if (pointerId !== null) {
		releasePointerCaptureIfNeeded(pointerId);
	}
	state.activePointerId = null;
}
