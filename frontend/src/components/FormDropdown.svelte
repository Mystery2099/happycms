<script lang="ts">
	import Dropdown from './Dropdown.svelte';

	interface Props {
		id?: string;
		name: string;
		options: string[];
		selected?: string;
		class?: string;
		ariaLabel?: string;
		ariaLabelledby?: string;
		ariaDescribedby?: string;
	}

	let {
		id,
		name,
		options,
		selected = '',
		class: className = '',
		ariaLabel = 'Select an option',
		ariaLabelledby,
		ariaDescribedby
	}: Props = $props();

	let initialValue = $derived(selected || options[0] || '');
	let value = $state('');
	let dropdownOptions = $derived(options.map((option) => ({ value: option, label: option })));

	$effect(() => {
		if (selected && selected !== value) {
			value = selected;
			return;
		}

		if (!value && initialValue) {
			value = initialValue;
		}
	});
</script>

<div class={className}>
	<input {id} type="hidden" {name} value={value} />
	<Dropdown
		options={dropdownOptions}
		bind:value
		ariaLabel={ariaLabel}
		ariaLabelledby={ariaLabelledby}
		ariaDescribedby={ariaDescribedby}
		listAriaLabel={ariaLabel}
	/>
</div>
