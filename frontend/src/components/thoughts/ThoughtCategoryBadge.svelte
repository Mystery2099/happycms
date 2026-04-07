<script lang="ts">
	interface Props {
		category: string;
		variant?: 'muted' | 'accent';
		rounded?: boolean;
	}

	let { category, variant = 'muted', rounded = false }: Props = $props();
</script>

<span
	class={[
		'inline-flex items-center px-2.5 py-0.5 text-xs font-medium',
		variant === 'accent' ? 'bg-coral/10 text-coral' : 'bg-mist/50 text-stone',
		rounded ? 'rounded-full py-1' : ''
	]}
>
	{category}
</span>
