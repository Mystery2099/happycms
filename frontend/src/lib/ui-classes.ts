// Shared surface tokens for the dropdown and account-menu family. Keep these
// limited to visual shell styles so behavior stays owned by each component.
export const mobileSheetPanelBaseClass =
	'pointer-events-auto overflow-hidden rounded-t-sheet border border-b-0 border-stone-200/80 bg-canvas shadow-sheet dark:border-slate-700 dark:bg-slate-900';

export const mobileSheetHeaderClass =
	'sticky top-0 z-10 bg-canvas/95 backdrop-blur-sm dark:bg-slate-900/95';

export const mobileSheetHandleButtonClass =
	'mx-auto mt-3 flex h-8 w-full cursor-grab items-center justify-center touch-none active:cursor-grabbing';

export const mobileSheetHandleBarClass = 'h-1.5 w-12 rounded-full bg-stone-300/80 dark:bg-slate-600';

export const mobileSheetCloseButtonClass =
	'flex h-11 w-11 items-center justify-center rounded-md bg-stone-200/70 text-stone transition-colors hover:bg-stone-300/80 hover:text-ink dark:bg-slate-800 dark:text-slate-300 dark:hover:bg-slate-700 dark:hover:text-slate-50';

export const desktopSurfaceTriggerBaseClass =
	'input-minimal hover:border-coral/40 flex min-h-[48px] items-center justify-between gap-3 rounded-md border border-mist/80 bg-canvas-elevated px-4 py-3 text-left text-sm font-medium text-ink shadow-sm transition-all duration-200 hover:-translate-y-px hover:bg-coral/5 hover:shadow-card focus:-translate-y-px dark:border-slate-700 dark:bg-slate-800/95 dark:hover:border-slate-500 dark:hover:bg-slate-700/95 dark:hover:text-slate-50';

export const desktopSurfaceTriggerOpenClass =
	'border-coral bg-canvas-elevated text-ink shadow-card dark:border-rose-400 dark:bg-slate-700 dark:text-slate-50';

export function getMobileSheetPanelClass(isDragging: boolean) {
	// Dragging disables transition classes so pointer movement stays 1:1 with the sheet.
	return `${mobileSheetPanelBaseClass} ${isDragging ? 'transition-none' : 'transition-transform duration-220 ease-out'}`;
}

export function getDesktopSurfaceTriggerClass({
	extraClass = '',
	isOpen = false
}: {
	extraClass?: string;
	isOpen?: boolean;
}) {
	return [
		desktopSurfaceTriggerBaseClass,
		extraClass,
		isOpen ? desktopSurfaceTriggerOpenClass : ''
	];
}
