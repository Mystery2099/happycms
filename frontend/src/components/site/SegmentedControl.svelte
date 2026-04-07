<script lang="ts">
	type SegmentedOption = {
		value: string;
		label: string;
		title?: string;
	};

	interface Props {
		options: SegmentedOption[];
		value: string;
		ariaLabel: string;
		groupClass?: string;
		buttonClass?: string;
	}

	let {
		options,
		value = $bindable(),
		ariaLabel,
		groupClass = '',
		buttonClass = 'px-3 py-1.5'
	}: Props = $props();
</script>

<div
	class={['bg-mist/50 flex items-center rounded-lg p-0.5 dark:bg-slate-800/50', groupClass]}
	role="group"
	aria-label={ariaLabel}
>
	{#each options as option (option.value)}
		<button
			type="button"
			class={[
				'rounded-md text-sm font-medium transition-all duration-200 active:scale-95',
				buttonClass,
				value === option.value
					? 'bg-white text-ink shadow-sm ring-2 ring-coral ring-offset-1 dark:bg-slate-700 dark:ring-offset-slate-800'
					: 'text-stone hover:text-ink'
			]}
			onclick={() => (value = option.value)}
			aria-pressed={value === option.value}
			title={option.title ?? option.label}
		>
			{option.label}
		</button>
	{/each}
</div>
