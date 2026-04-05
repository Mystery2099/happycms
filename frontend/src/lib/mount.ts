import { mount } from 'svelte';
import type { Component, ComponentProps } from 'svelte';

export type JsonProps = Record<string, unknown>;
export type ComponentLoader<TComponent extends Component<any>> = () => Promise<{
	default: TComponent;
}>;

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
