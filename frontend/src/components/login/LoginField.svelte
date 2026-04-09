<script lang="ts">
	import type { Snippet } from 'svelte';
	import type { IconComponent } from '../../lib/types';

	interface Props {
		id: string;
		name: string;
		label: string;
		icon: IconComponent;
		type?: 'email' | 'password' | 'text';
		value?: string;
		placeholder?: string;
		required?: boolean;
		autocomplete?: string;
		trailingControl?: Snippet;
	}

	let {
		id,
		name,
		label,
		icon,
		type = 'text',
		value = $bindable(''),
		placeholder = '',
		required = false,
		autocomplete,
		trailingControl
	}: Props = $props();

	const Icon = $derived(icon);
	const inputClass = $derived(
		[
			'w-full rounded-md border-b-2 border-mist bg-transparent py-3 pl-10 text-ink placeholder:text-stone/85 transition-colors focus:border-coral focus:outline-none dark:border-slate-600 dark:text-slate-100 dark:placeholder:text-slate-400',
			trailingControl ? 'pr-12' : 'pr-4'
		]
	);
</script>

<div class="space-y-2">
	<label for={id} class="block text-sm font-medium text-ink">{label}</label>
	<div class="relative">
		<div class="pointer-events-none absolute top-0 bottom-0 left-0 flex items-center pl-3">
			<Icon size={16} class="text-stone" />
		</div>
		<input {id} {name} {type} bind:value {placeholder} {required} {autocomplete} class={inputClass} />
		{#if trailingControl}
			<div class="absolute top-0 right-0 bottom-0 flex items-center pr-3">
				{@render trailingControl()}
			</div>
		{/if}
	</div>
</div>
