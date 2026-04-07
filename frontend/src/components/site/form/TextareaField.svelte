<script lang="ts">
	import type { IconComponent } from '../../../lib/types';

	interface Props {
		id: string;
		name: string;
		label: string;
		icon?: IconComponent;
		value?: string;
		placeholder?: string;
		required?: boolean;
		rows?: number;
		minlength?: number;
		maxlength?: number;
		error?: string;
		errorId?: string;
		hint?: string;
		hintId?: string;
	}

	let {
		id,
		name,
		label,
		icon,
		value = '',
		placeholder = '',
		required = false,
		rows = 5,
		minlength,
		maxlength,
		error = '',
		errorId,
		hint = '',
		hintId
	}: Props = $props();

	const Icon = $derived(icon);
	const describedBy = $derived(
		[hintId, error ? errorId : undefined].filter(Boolean).join(' ') || undefined
	);
</script>

<div>
	<label for={id} class="mb-2 inline-flex items-center gap-2 text-sm font-medium text-stone">
		{#if Icon}
			<Icon size={16} />
		{/if}
		{label}
	</label>
	<textarea
		{id}
		{name}
		{rows}
		class="input-minimal resize-none"
		{placeholder}
		{required}
		{minlength}
		{maxlength}
		aria-invalid={error ? 'true' : undefined}
		aria-describedby={describedBy}
	>{value}</textarea>
	{#if hint}
		<p id={hintId} class="mt-2 text-xs text-stone">{hint}</p>
	{/if}
	{#if error}
		<p id={errorId} class="mt-2 text-sm text-coral">{error}</p>
	{/if}
</div>
