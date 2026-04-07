<script lang="ts">
	interface Props {
		score: number;
		className?: string;
	}

	let { score, className = '' }: Props = $props();

	const mood = $derived('★'.repeat(score));
</script>

<span class={['text-wheat', className]}>{mood}</span>
