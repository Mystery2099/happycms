import { tick } from 'svelte';
import type { DropdownOption } from '../lib/dropdown';

export function getSelectedIndex(options: DropdownOption[], value: string): number {
	const index = options.findIndex((opt) => opt.value === value);
	return index >= 0 ? index : 0;
}

export function focusOption(elements: HTMLButtonElement[], index: number) {
	tick().then(() => {
		elements[index]?.focus();
	});
}

export function moveHighlight(
	options: DropdownOption[],
	highlightedIndex: number,
	value: string,
	step: number,
	elements: HTMLButtonElement[]
): number {
	if (options.length === 0) return highlightedIndex;

	const startIndex = highlightedIndex >= 0 ? highlightedIndex : getSelectedIndex(options, value);
	const nextIndex = (startIndex + step + options.length) % options.length;
	focusOption(elements, nextIndex);
	return nextIndex;
}

export function jumpHighlight(
	options: DropdownOption[],
	index: number,
	elements: HTMLButtonElement[]
): number {
	if (options.length === 0) return 0;

	const boundedIndex = Math.max(0, Math.min(index, options.length - 1));
	focusOption(elements, boundedIndex);
	return boundedIndex;
}