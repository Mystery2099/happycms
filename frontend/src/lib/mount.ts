import { mount } from 'svelte';

export type JsonProps = Record<string, unknown>;
export type ComponentLoader<TComponent> = () => Promise<{ default: TComponent }>;

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

export function mountPage<TComponent>(
	selector: string,
	scriptId: string,
	loader: ComponentLoader<TComponent>
): void {
	const target = document.querySelector<HTMLElement>(selector);
	if (!target) {
		return;
	}

	const props = parseJsonScript<JsonProps>(scriptId);
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
