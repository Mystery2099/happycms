<script lang="ts">
	import Dropdown from './Dropdown.svelte';

	interface Props {
		name: string;
		options: string[];
		selected?: string;
		class?: string;
		ariaLabel?: string;
	}

	let {
		name,
		options,
		selected = '',
		class: className = '',
		ariaLabel = 'Select an option'
	}: Props = $props();

	let initialValue = $derived(selected || options[0] || '');
	let value = $state('');
	let dropdownOptions = $derived(options.map((option) => ({ value: option, label: option })));

	$effect(() => {
		if (!value && initialValue) {
			value = initialValue;
		}
	});
</script>

<div class={className}>
	<input type="hidden" {name} value={value} />
	<Dropdown options={dropdownOptions} bind:value ariaLabel={ariaLabel} listAriaLabel={ariaLabel} />
</div>
