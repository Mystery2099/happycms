<script lang="ts">
	import { Pencil, Trash2 } from '@lucide/svelte';

	interface Props {
		editUrl: string | null;
		deleteUrl: string | null;
		layout?: 'table' | 'card';
	}

	let { editUrl, deleteUrl, layout = 'card' }: Props = $props();

	let hasActions = $derived(Boolean(editUrl && deleteUrl));
</script>

{#if hasActions}
	{#if layout === 'table'}
		<div class="flex items-center justify-end gap-2">
			<a
				href={editUrl ?? undefined}
				class="text-sm text-stone inline-flex min-h-11 items-center gap-1.5 px-2 py-2 transition-colors hover:text-ink"
			>
				<Pencil size={14} />
				Edit
			</a>
			<span class="text-mist">|</span>
			<a
				href={deleteUrl ?? undefined}
				class="text-coral text-sm inline-flex min-h-11 items-center gap-1.5 px-2 py-2 transition-colors hover:text-ink"
			>
				<Trash2 size={14} />
				Delete
			</a>
		</div>
	{:else}
		<div class="flex items-center gap-4">
			<a
				href={editUrl ?? undefined}
				class="text-stone flex min-h-11 items-center gap-1.5 px-2 py-2 text-sm font-medium transition-colors hover:text-ink"
			>
				<Pencil size={14} />
				Edit
			</a>
			<a
				href={deleteUrl ?? undefined}
				class="text-coral flex min-h-11 items-center gap-1.5 px-2 py-2 text-sm font-medium transition-colors hover:text-ink"
			>
				<Trash2 size={14} />
				Delete
			</a>
		</div>
	{/if}
{/if}
