<script lang="ts">
	import { tick } from 'svelte';
	import { listFocusableElements } from '../lib/focus';
	import { portal } from '../lib/portal';

	interface Props {
		panelId?: string;
		panelDataId?: string;
		isVisible: boolean;
		isOverlayVisible?: boolean;
		closeLabel?: string;
		ariaLabelledby?: string;
		ariaDescribedby?: string;
		panelClass?: string;
		sheetElement?: HTMLElement | null;
		initialFocusElement?: HTMLElement | null;
		onClose?: () => void;
		children?: () => unknown;
	}

	let {
		panelId,
		panelDataId,
		isVisible,
		isOverlayVisible = true,
		closeLabel = 'Close panel',
		ariaLabelledby,
		ariaDescribedby,
		panelClass = '',
		sheetElement = $bindable<HTMLElement | null>(null),
		initialFocusElement = null,
		onClose,
		children
	}: Props = $props();

	function getFocusableElements() {
		return listFocusableElements(sheetElement);
	}

	function trapFocus(event: KeyboardEvent) {
		if (event.key !== 'Tab') return;

		const focusableElements = getFocusableElements();
		if (focusableElements.length === 0) {
			event.preventDefault();
			return;
		}

		const firstElement = focusableElements[0];
		const lastElement = focusableElements[focusableElements.length - 1];
		const activeElement = document.activeElement as HTMLElement | null;
		const focusIsInsideSheet = !!(activeElement && sheetElement?.contains(activeElement));

		if (!focusIsInsideSheet) {
			event.preventDefault();
			(event.shiftKey ? lastElement : firstElement).focus();
			return;
		}

		if (event.shiftKey && activeElement === firstElement) {
			event.preventDefault();
			lastElement.focus();
			return;
		}

		if (!event.shiftKey && activeElement === lastElement) {
			event.preventDefault();
			firstElement.focus();
		}
	}

	function keepFocusInsideSheet(event: FocusEvent) {
		if (!isVisible || !sheetElement) return;

		const nextTarget = event.target as HTMLElement | null;
		if (!nextTarget || sheetElement.contains(nextTarget)) return;

		const [firstElement] = getFocusableElements();
		(firstElement ?? initialFocusElement)?.focus();
	}

	function handleDocumentKeydown(event: KeyboardEvent) {
		if (!isVisible) return;

		trapFocus(event);
		if (event.key === 'Escape') {
			onClose?.();
		}
	}

	$effect(() => {
		if (!isVisible) return;

		const previousOverflow = document.body.style.overflow;
		document.body.style.overflow = 'hidden';
		document.addEventListener('focusin', keepFocusInsideSheet);

		return () => {
			document.body.style.overflow = previousOverflow;
			document.removeEventListener('focusin', keepFocusInsideSheet);
		};
	});

	$effect(() => {
		if (!isVisible) return;

		tick().then(() => {
			initialFocusElement?.focus();
		});
	});
</script>

<svelte:document onkeydown={handleDocumentKeydown} />

{#if isVisible}
	<div use:portal>
		<div class="fixed inset-0 z-[140]" aria-hidden="true">
			<button
				type="button"
				class="absolute inset-0 bg-slate-950/45 backdrop-blur-[2px] transition-opacity duration-180 ease-out {isOverlayVisible ? 'opacity-100' : 'opacity-0'}"
				onclick={() => onClose?.()}
				tabindex="-1"
				aria-label={closeLabel}
			></button>
		</div>

		<div class="fixed inset-x-0 bottom-0 z-[150] pointer-events-none">
			<div
				bind:this={sheetElement}
				id={panelId}
				data-mobile-sheet-id={panelDataId}
				class={panelClass}
				role="dialog"
				aria-modal="true"
				aria-labelledby={ariaLabelledby}
				aria-describedby={ariaDescribedby}
			>
				{@render children?.()}
			</div>
		</div>
	</div>
{/if}
