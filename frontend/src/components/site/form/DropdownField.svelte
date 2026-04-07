<script lang="ts">
	import type { IconComponent } from '../../../lib/types';
	import FormDropdown from '../../FormDropdown.svelte';

	interface Props {
		name: string;
		label: string;
		labelId: string;
		options: string[];
		selected: string;
		icon?: IconComponent;
		error?: string;
		errorId?: string;
	}

	let { name, label, labelId, options, selected, icon, error = '', errorId }: Props = $props();

	const Icon = $derived(icon);
</script>

<div>
	<label id={labelId} class="mb-2 inline-flex items-center gap-2 text-sm font-medium text-stone">
		{#if Icon}
			<Icon size={16} />
		{/if}
		{label}
	</label>
	<FormDropdown
		{name}
		{options}
		{selected}
		ariaLabel={label}
		ariaLabelledby={labelId}
		ariaDescribedby={error ? errorId : undefined}
	/>
	{#if error}
		<p id={errorId} class="mt-2 text-sm text-coral">{error}</p>
	{/if}
</div>
