<script lang="ts">
	import type { Snippet } from 'svelte';
	import type { HTMLInputAttributes } from 'svelte/elements';
	import type { IconComponent } from '../../../lib/types';

	interface Props extends Omit<HTMLInputAttributes, 'children' | 'class'> {
		id: string;
		label: string;
		icon: IconComponent;
		labelSuffix?: Snippet;
		trailingControl?: Snippet;
		className?: string;
	}

	let {
		id,
		label,
		icon,
		labelSuffix,
		trailingControl,
		className = '',
		...inputProps
	}: Props = $props();

	const Icon = $derived(icon);
	const inputClass = $derived(
		[
			'w-full rounded-md border-b-2 border-mist bg-transparent py-3 pl-10 text-ink placeholder:text-stone/85 transition-colors focus:border-coral focus:outline-none dark:border-slate-600 dark:text-slate-100 dark:placeholder:text-slate-400',
			trailingControl ? 'pr-12' : 'pr-4',
			className
		]
	);
</script>

<div class="space-y-2">
	<div class="flex items-center justify-between gap-3">
		<label for={id} class="block text-sm font-medium text-ink">{label}</label>
		{@render labelSuffix?.()}
	</div>
	<div class="relative">
		<div class="pointer-events-none absolute top-0 bottom-0 left-0 flex items-center pl-3">
			<Icon size={16} class="text-stone" />
		</div>
		<input {id} class={inputClass} {...inputProps} />
		{#if trailingControl}
			<div class="absolute top-0 right-0 bottom-0 flex items-center pr-3">
				{@render trailingControl()}
			</div>
		{/if}
	</div>
</div>
