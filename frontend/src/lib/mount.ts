import { mount } from 'svelte';
import type { Component, ComponentProps } from 'svelte';

export type JsonProps = Record<string, unknown>;
export type ComponentLoader<TComponent extends Component<any>> = () => Promise<{
	default: TComponent;
}>;

/**
 * Read server-rendered page props from an inline JSON script tag. Returning
 * `null` keeps partial pages safe when a mount point exists without payload data.
 */
export function parseJsonScript<T>(scriptId: string): T | null {
	const element = document.getElementById(scriptId);

	if (!(element instanceof HTMLScriptElement)) {
		return null;
	}

	try {
		return JSON.parse(element.textContent ?? '') as T;
	} catch {
		return null;
	}
}

/**
 * Mount a lazily loaded Svelte page component only when both the target element
 * and its serialized props are present on the current document.
 */
export function mountPage<TComponent extends Component<any>>(
	selector: string,
	scriptId: string,
	loader: ComponentLoader<TComponent>
): void {
	const target = document.querySelector<HTMLElement>(selector);
	if (!target) {
		return;
	}

	const props = parseJsonScript<ComponentProps<TComponent>>(scriptId);
	if (!props) {
		return;
	}

	loader().then(({ default: Component }) => {
		mount(Component, {
			target,
			props
		});
	});
}
