export type SheetDragState = {
	activePointerId: number | null;
	dragStartY: number;
	dragStartOffset: number;
	dragDeltaY: number;
	suppressHandleClick: boolean;
	isDragging: boolean;
};

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
