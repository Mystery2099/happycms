<script lang="ts">
	interface Props {
		type: 'success' | 'error';
		message: string;
	}

	let { type, message }: Props = $props();
</script>

<div class="border-b border-mist bg-canvas-elevated rounded-b-lg">
	<div class="max-w-6xl mx-auto px-6 py-4 lg:px-8">
		<div
			class={[
				'flex items-center gap-3 text-sm',
				type === 'success' ? 'text-moss' : 'text-coral'
			]}
			role={type === 'error' ? 'alert' : 'status'}
			aria-live={type === 'error' ? 'assertive' : 'polite'}
			aria-atomic="true"
		>
			<span>{type === 'success' ? '✓' : '!'}</span>
			{message}
		</div>
	</div>
</div>
