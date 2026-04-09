<script lang="ts">
	import { Pencil, Trash2 } from '@lucide/svelte';

	interface Props {
		editUrl: string | null;
		deleteUrl: string | null;
		layout?: 'table' | 'card';
	}

	let { editUrl, deleteUrl, layout = 'card' }: Props = $props();

	let hasActions = $derived(Boolean(editUrl && deleteUrl));
	const isTableLayout = $derived(layout === 'table');
	const containerClass = $derived(
		isTableLayout ? 'flex items-center justify-end gap-2' : 'flex items-center gap-4'
	);
	const editActionClass = $derived(
		[
			'inline-flex min-h-11 items-center gap-1.5 rounded-md px-2 py-2 text-sm transition-all hover:bg-coral/5 hover:text-ink',
			isTableLayout ? 'text-stone' : 'text-stone font-medium'
		].join(' ')
	);
	const deleteActionClass = $derived(
		[
			'inline-flex min-h-11 items-center gap-1.5 rounded-md px-2 py-2 text-sm transition-all hover:bg-coral/10 hover:text-coral',
			isTableLayout ? 'text-coral' : 'text-coral font-medium'
		].join(' ')
	);
</script>

{#if hasActions}
	<div class={containerClass}>
		<a href={editUrl ?? undefined} class={editActionClass}>
				<Pencil size={14} />
				Edit
		</a>
		{#if isTableLayout}
			<span class="text-mist">|</span>
		{/if}
		<a href={deleteUrl ?? undefined} class={deleteActionClass}>
			<Trash2 size={14} />
			Delete
		</a>
	</div>
{/if}
