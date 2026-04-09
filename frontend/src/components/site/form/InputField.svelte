<script lang="ts">
  import type { IconComponent } from "../../../lib/types";

  interface Props {
    id: string;
    name: string;
    label: string;
    icon?: IconComponent;
    type?: string;
    value?: string | number;
    placeholder?: string;
    required?: boolean;
    min?: string | number;
    max?: string | number;
    minlength?: number;
    maxlength?: number;
    error?: string;
    errorId?: string;
    hint?: string;
    hintId?: string;
  }

  let {
    id,
    name,
    label,
    icon,
    type = "text",
    value = "",
    placeholder = "",
    required = false,
    min,
    max,
    minlength,
    maxlength,
    error = "",
    errorId,
    hint = "",
    hintId,
  }: Props = $props();

  const Icon = $derived(icon);
  const describedBy = $derived(
    [hintId, error ? errorId : undefined].filter(Boolean).join(" ") ||
      undefined,
  );
</script>

<div>
  <label
    for={id}
    class="mb-2 inline-flex items-center gap-2 text-sm font-medium text-stone"
  >
    {#if Icon}
      <Icon size={16} />
    {/if}
    {label}
  </label>
  <input
    {id}
    {type}
    {name}
    {value}
    {placeholder}
    {required}
    {min}
    {max}
    {minlength}
    {maxlength}
    class="input-minimal"
    aria-invalid={error ? "true" : undefined}
    aria-describedby={describedBy}
  />
  {#if hint}
    <p id={hintId} class="mt-2 text-xs text-stone">{hint}</p>
  {/if}
  {#if error}
    <p id={errorId} class="mt-2 text-sm text-coral">{error}</p>
  {/if}
</div>
