import {
	SHEET_EXPANDED_TOP_OFFSET_PX,
	SHEET_MIN_VIEWPORT_RATIO,
	SHEET_MIN_ABSOLUTE_HEIGHT_PX,
	SHEET_HEADER_OFFSET_PX
} from './sheet-constants';

export function getExpandedTopOffset(): number {
	return SHEET_EXPANDED_TOP_OFFSET_PX;
}

export function getMinimumVisibleHeight(viewportHeight: number): number {
	return Math.max(viewportHeight * SHEET_MIN_VIEWPORT_RATIO, SHEET_MIN_ABSOLUTE_HEIGHT_PX);
}

export function getMaximumSheetHeight(viewportHeight: number): number {
	return Math.max(viewportHeight - getExpandedTopOffset(), getMinimumVisibleHeight(viewportHeight));
}

export function getCollapsedOffset(viewportHeight: number): number {
	return Math.max(getMaximumSheetHeight(viewportHeight) - getMinimumVisibleHeight(viewportHeight), 0);
}

export function getScrollContentHeight(viewportHeight: number): number {
	return Math.max(getMaximumSheetHeight(viewportHeight) - SHEET_HEADER_OFFSET_PX, 220);
}

export function getRestingTranslateY(viewportHeight: number, isExpanded: boolean): number {
	return isExpanded ? 0 : getCollapsedOffset(viewportHeight);
}