<script lang="ts">
  import { Pencil, Trash2 } from "@lucide/svelte";

  interface Props {
    editUrl: string | null;
    deleteUrl: string | null;
    layout?: "table" | "card";
  }

  let { editUrl, deleteUrl, layout = "card" }: Props = $props();

  const isTableLayout = $derived(layout === "table");
  const actionBaseClass =
    "inline-flex min-h-11 items-center gap-1.5 rounded-md px-2 py-2 text-sm transition-all";
  const containerClass = $derived(
    isTableLayout
      ? "flex items-center justify-end gap-2"
      : "flex items-center gap-4",
  );

  function getActionClass({
    tone,
    emphasize = false,
  }: {
    tone: "default" | "destructive";
    emphasize?: boolean;
  }) {
    const toneClass =
      tone === "destructive"
        ? "text-coral hover:bg-coral/10 hover:text-coral"
        : "text-stone hover:bg-coral/5 hover:text-ink";

    return [actionBaseClass, toneClass, emphasize ? "font-medium" : ""].join(
      " ",
    );
  }

  const editActionClass = $derived(
    getActionClass({ tone: "default", emphasize: !isTableLayout }),
  );
  const deleteActionClass = $derived(
    getActionClass({ tone: "destructive", emphasize: !isTableLayout }),
  );
</script>

{#if editUrl && deleteUrl}
  <div class={containerClass}>
    <a href={editUrl} class={editActionClass}>
      <Pencil size={14} />
      Edit
    </a>
    {#if isTableLayout}
      <span class="text-mist">|</span>
    {/if}
    <a href={deleteUrl} class={deleteActionClass}>
      <Trash2 size={14} />
      Delete
    </a>
  </div>
{/if}
