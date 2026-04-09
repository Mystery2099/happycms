<script lang="ts">
	import { onMount } from 'svelte';

	interface Props {
		type: 'success' | 'error';
		message: string;
	}

	let { type, message }: Props = $props();
	let isMounted = $state(false);

	onMount(() => {
		requestAnimationFrame(() => {
			isMounted = true;
		});
	});
</script>

<div
	class={['border-b border-mist bg-canvas-elevated rounded-b-lg transition-[transform,opacity] duration-250 ease-enter motion-reduce:transition-none', isMounted ? 'translate-y-0 opacity-100' : '-translate-y-full opacity-0']}
	role={type === 'error' ? 'alert' : 'status'}
	aria-live={type === 'error' ? 'assertive' : 'polite'}
	aria-atomic="true"
>
	<div class="max-w-6xl mx-auto px-6 py-4 lg:px-8">
		<div
			class={[
				'flex items-center gap-3 text-sm',
				type === 'success' ? 'text-moss' : 'text-coral'
			]}
		>
			<span class={['inline-flex items-center justify-center w-5 h-5 rounded-full font-semibold text-xs leading-none', type === 'success' ? 'bg-moss/15 text-moss' : 'bg-coral/15 text-coral']} aria-hidden="true">{type === 'success' ? '✓' : '!'}</span>
			{message}
		</div>
	</div>
</div>