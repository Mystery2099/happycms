<script lang="ts">
	import { Image } from '@lucide/svelte';
	import type { ThoughtRecord } from '../../lib/types';
	import ThoughtActions from './ThoughtActions.svelte';
	import ThoughtCategoryBadge from './ThoughtCategoryBadge.svelte';
	import ThoughtMood from './ThoughtMood.svelte';

	interface Props {
		thoughts: ThoughtRecord[];
		rowClass?: string;
		rowPadding?: string;
	}

	let { thoughts, rowClass = 'py-5', rowPadding = '1.25rem' }: Props = $props();
</script>

<div class="border-mist overflow-hidden border rounded-lg">
	<table class="data-table">
		<thead>
			<tr>
				<th>Thought</th>
				<th>Author</th>
				<th>Category</th>
				<th>Mood</th>
				<th class="text-right">Actions</th>
			</tr>
		</thead>
		<tbody>
			{#each thoughts as thought (thought.id)}
				<tr>
					<td class={['max-w-md', rowClass]} style:padding-block={rowPadding}>
						<p class="font-medium text-ink">{thought.title}</p>
						<p class="mt-1 text-sm text-stone whitespace-normal break-words">{thought.thought}</p>
						{#if thought.imageUrl}
							<span class="mt-2 inline-flex items-center gap-1 text-xs text-stone">
								<Image size={12} />
								Has image
							</span>
						{/if}
					</td>
					<td class={['text-stone', rowClass]} style:padding-block={rowPadding}>{thought.author}</td>
					<td class={rowClass} style:padding-block={rowPadding}>
						<ThoughtCategoryBadge category={thought.category} />
					</td>
					<td class={['text-wheat', rowClass]} style:padding-block={rowPadding}>
						<ThoughtMood score={thought.moodScore} />
					</td>
					<td class={['text-right', rowClass]} style:padding-block={rowPadding}>
						<ThoughtActions editUrl={thought.editUrl} deleteUrl={thought.deleteUrl} layout="table" />
					</td>
				</tr>
			{/each}
		</tbody>
	</table>
</div>
