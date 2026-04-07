<script lang="ts">
	import type { IconComponent } from '../../lib/types';

	type SegmentedOption = {
		value: string;
		label: string;
		title?: string;
		icon?: IconComponent;
	};

	interface Props {
		options: SegmentedOption[];
		value: string;
		ariaLabel: string;
		groupClass?: string;
		buttonClass?: string;
		onChange?: (value: string) => void;
	}

	let {
		options,
		value = $bindable(),
		ariaLabel,
		groupClass = '',
		buttonClass = 'px-3 py-1.5',
		onChange
	}: Props = $props();

	function selectOption(nextValue: string) {
		value = nextValue;
		onChange?.(nextValue);
	}
</script>

<div
	class={['bg-mist/50 grid items-center rounded-lg p-0.5 dark:bg-slate-800/50', groupClass]}
	role="group"
	aria-label={ariaLabel}
	style:grid-template-columns={`repeat(${options.length}, minmax(0, 1fr))`}
>
	{#each options as option (option.value)}
		{@const Icon = option.icon}
		<button
			type="button"
			class={[
				'min-w-0 flex items-center justify-center gap-2 rounded-md text-sm font-medium transition-all duration-200 active:scale-95',
				buttonClass,
				value === option.value
					? 'bg-white text-ink shadow-sm ring-2 ring-coral ring-offset-1 dark:bg-slate-700 dark:ring-offset-slate-800'
					: 'text-stone hover:text-ink'
			]}
			onclick={() => selectOption(option.value)}
			aria-pressed={value === option.value}
			title={option.title ?? option.label}
		>
			{#if Icon}
				<Icon size={14} />
			{/if}
			<span class="truncate">{option.label}</span>
		</button>
	{/each}
</div>
