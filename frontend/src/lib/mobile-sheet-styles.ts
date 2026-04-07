type SheetStyleManager = {
	mount: () => void;
	update: (config: {
		isActive: boolean;
		height: number;
		maxHeight: string;
		translateY: number;
		scrollHeight: number;
		allowScroll: boolean;
	}) => void;
	clear: () => void;
	destroy: () => void;
};

export function createSheetStyleManager(sheetDataId: string): SheetStyleManager {
	let styleElement: HTMLStyleElement | null = null;
	let sheetRule: CSSStyleRule | null = null;
	let scrollRule: CSSStyleRule | null = null;

	function mount() {
		const nonce = document
			.querySelector<HTMLMetaElement>('meta[name="csp-nonce"]')
			?.getAttribute('content');

		styleElement = document.createElement('style');
		if (nonce) {
			styleElement.setAttribute('nonce', nonce);
		}

		document.head.appendChild(styleElement);
		const stylesheet = styleElement.sheet as CSSStyleSheet | null;
		if (!stylesheet) return;

		const sheetSelector = `[data-mobile-sheet-id="${sheetDataId}"]`;
		const scrollSelector = `${sheetSelector} .dropdown-sheet-scroll`;
		stylesheet.insertRule(`${sheetSelector} {}`, 0);
		stylesheet.insertRule(`${scrollSelector} {}`, 1);
		sheetRule = stylesheet.cssRules[0] as CSSStyleRule;
		scrollRule = stylesheet.cssRules[1] as CSSStyleRule;
	}

	function clear() {
		if (!sheetRule || !scrollRule) return;
		sheetRule.style.cssText = '';
		scrollRule.style.cssText = '';
	}

	function update({
		isActive,
		height,
		maxHeight,
		translateY,
		scrollHeight,
		allowScroll
	}: {
		isActive: boolean;
		height: number;
		maxHeight: string;
		translateY: number;
		scrollHeight: number;
		allowScroll: boolean;
	}) {
		if (!sheetRule || !scrollRule) return;
		if (!isActive) {
			clear();
			return;
		}

		sheetRule.style.height = `${height}px`;
		sheetRule.style.maxHeight = maxHeight;
		sheetRule.style.transform = `translateY(${Math.max(0, translateY)}px)`;
		scrollRule.style.maxHeight = `${scrollHeight}px`;
		scrollRule.style.overflowY = allowScroll ? 'auto' : 'hidden';
	}

	function destroy() {
		styleElement?.remove();
		styleElement = null;
		sheetRule = null;
		scrollRule = null;
	}

	return { mount, update, clear, destroy };
}
