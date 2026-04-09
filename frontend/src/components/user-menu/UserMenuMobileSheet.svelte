<script lang="ts">
	import { Palette } from '@lucide/svelte';
	import MobileSheet from '../MobileSheet.svelte';
	import ThemeSelector from '../ThemeSelector.svelte';
	import UserMenuAccountCard from './UserMenuAccountCard.svelte';
	import UserMenuAuthAction from './UserMenuAuthAction.svelte';

	interface Props {
		isVisible: boolean;
		isOverlayVisible: boolean;
		isClosingSheet: boolean;
		isDragging: boolean;
		isAnimatingIntro: boolean;
		currentTranslateY: number;
		menuId: string;
		sheetTitleId: string;
		sheetElement?: HTMLElement | null;
		sheetCloseRef?: HTMLButtonElement | null;
		sheetHandleRef?: HTMLButtonElement | null;
		firstActionRef?: HTMLAnchorElement | HTMLButtonElement | null;
		isLoggedIn: boolean;
		userName: string;
		userEmail: string;
		loginUrl: string;
		logoutUrl: string;
		logoutCsrfToken?: string;
		onClose: () => void;
		onNavigate?: () => void;
		onHandlePointerDown: (event: PointerEvent) => void;
		onHandlePointerMove: (event: PointerEvent) => void;
		onHandlePointerEnd: (event: PointerEvent) => void;
		onHandleClick: (event: MouseEvent) => void;
	}

	let {
		isVisible,
		isOverlayVisible,
		isClosingSheet,
		isDragging,
		isAnimatingIntro,
		currentTranslateY,
		menuId,
		sheetTitleId,
		sheetElement = $bindable(),
		sheetCloseRef = $bindable(),
		sheetHandleRef = $bindable(),
		firstActionRef = $bindable(),
		isLoggedIn,
		userName,
		userEmail,
		loginUrl,
		logoutUrl,
		logoutCsrfToken = '',
		onClose,
		onNavigate,
		onHandlePointerDown,
		onHandlePointerMove,
		onHandlePointerEnd,
		onHandleClick
	}: Props = $props();
</script>

{#if isVisible}
	<MobileSheet
		isVisible={isVisible}
		isOverlayVisible={isOverlayVisible}
		closeLabel="Close site menu"
		ariaLabelledby={sheetTitleId}
		bind:sheetElement
		initialFocusElement={sheetCloseRef}
		{onClose}
		panelClass="pointer-events-auto overflow-hidden rounded-t-sheet border border-b-0 border-stone-200/80 bg-canvas shadow-sheet dark:border-slate-700 dark:bg-slate-900 {isDragging ? 'transition-none' : 'transition-transform duration-220 ease-out'}"
		panelStyle={`transform: translateY(${Math.max(0, currentTranslateY)}px);`}
	>
		<div id={menuId}>
			<div class="sticky top-0 z-10 bg-canvas/95 backdrop-blur-sm dark:bg-slate-900/95">
				<button
					bind:this={sheetHandleRef}
					type="button"
					class="mx-auto mt-3 flex h-8 w-full cursor-grab items-center justify-center touch-none active:cursor-grabbing"
					onpointerdown={onHandlePointerDown}
					onpointermove={onHandlePointerMove}
					onpointerup={onHandlePointerEnd}
					onpointercancel={onHandlePointerEnd}
					onclick={onHandleClick}
					aria-label="Drag to close site menu"
				>
					<span class="h-1.5 w-12 rounded-full bg-stone-300/80 dark:bg-slate-600"></span>
				</button>
				<div class="flex items-center justify-between border-b border-stone-200/70 px-5 pb-3 pt-3 dark:border-slate-800">
					<div>
						<p class="text-xs font-semibold uppercase tracking-[0.2em] text-stone dark:text-slate-400">
							Menu
						</p>
						<h2 id={sheetTitleId} class="text-base font-medium text-ink dark:text-slate-50">
							{isLoggedIn ? userName || 'Preferences and account' : 'Appearance and account'}
						</h2>
					</div>
					<button
						bind:this={sheetCloseRef}
						type="button"
						class="flex h-11 w-11 items-center justify-center rounded-md bg-stone-200/70 text-stone transition-colors hover:bg-stone-300/80 hover:text-ink dark:bg-slate-800 dark:text-slate-300 dark:hover:bg-slate-700 dark:hover:text-slate-50"
						onclick={onClose}
						aria-label="Close site menu"
					>
						<span class="text-lg leading-none">×</span>
					</button>
				</div>
			</div>

			<div
				class="max-h-[calc(100dvh-7rem)] overflow-y-auto px-4 pb-[max(1rem,env(safe-area-inset-bottom))] pt-4"
				aria-busy={isClosingSheet || isAnimatingIntro}
			>
				<div class="space-y-4 pb-4">
					<div class="rounded-lg bg-mist/35 px-4 py-4 dark:bg-slate-800/60">
						<div class="mb-3 flex items-center gap-2 text-xs font-semibold uppercase tracking-[0.18em] text-stone dark:text-slate-400">
							<Palette size={14} />
							Theme
						</div>
						<ThemeSelector variant="mobile" />
					</div>

					<UserMenuAccountCard {isLoggedIn} {userName} {userEmail} variant="mobile" />

					<UserMenuAuthAction
						{isLoggedIn}
						{loginUrl}
						{logoutUrl}
						{logoutCsrfToken}
						variant="mobile"
						bind:firstActionRef
						{onNavigate}
					/>
				</div>
			</div>
		</div>
	</MobileSheet>
{/if}
