<script lang="ts">
	import type { ThoughtRecord } from '../../lib/types';
	import ThoughtActions from './ThoughtActions.svelte';
	import ThoughtCategoryBadge from './ThoughtCategoryBadge.svelte';
	import ThoughtMood from './ThoughtMood.svelte';

	interface Props {
		thoughts: ThoughtRecord[];
		gridClass: string;
		bodyClass: string;
		titleClass: string;
		metaClass: string;
		actionRowClass: string;
	}

	let { thoughts, gridClass, bodyClass, titleClass, metaClass, actionRowClass }: Props = $props();
</script>

<div class={`grid ${gridClass}`}>
	{#each thoughts as thought (thought.id)}
		<article
			class="group overflow-hidden rounded-xl border border-mist bg-white transition-all duration-200 hover:shadow-md"
		>
			{#if thought.imageUrl}
				<div class="aspect-[16/10] overflow-hidden">
					<img
						src={thought.imageUrl}
						alt={thought.title}
						class="h-full w-full object-cover transition-transform duration-500 group-hover:scale-105"
					/>
				</div>
			{/if}
			<div class={bodyClass}>
				<div class="flex items-start justify-between gap-3">
					<h3 class={['font-display leading-tight text-ink', titleClass]}>{thought.title}</h3>
					<ThoughtCategoryBadge category={thought.category} variant="accent" rounded={true} />
				</div>
				<p class="text-sm leading-relaxed text-stone whitespace-normal break-words">{thought.thought}</p>
				<div class={['border-mist/60 flex items-center justify-between border-t', metaClass]}>
					<span class="text-sm font-medium text-ink">{thought.author}</span>
					<ThoughtMood score={thought.moodScore} className="text-sm" />
				</div>
				<div class={['flex items-center', actionRowClass]}>
					<ThoughtActions editUrl={thought.editUrl} deleteUrl={thought.deleteUrl} />
				</div>
			</div>
		</article>
	{/each}
</div>
