<script lang="ts">
  import type { DropdownOption } from "../../lib/dropdown";

  interface Props {
    options: DropdownOption[];
    value: string;
    highlightedIndex: number;
    listboxId: string;
    menuClass?: string;
    defaultMenuClass: string;
    optionClass?: string;
    defaultOptionClass: string;
    listAriaLabel?: string;
    ariaLabel?: string;
    ariaLabelledby?: string;
    ariaDescribedby?: string;
    selectedLabel: string;
    optionElements?: HTMLButtonElement[];
    onSelect: (value: string) => void;
    onOptionKeydown: (event: KeyboardEvent, index: number) => void;
  }

  let {
    options,
    value,
    highlightedIndex,
    listboxId,
    menuClass = "",
    defaultMenuClass,
    optionClass = "",
    defaultOptionClass,
    listAriaLabel,
    ariaLabel,
    ariaLabelledby,
    ariaDescribedby,
    selectedLabel,
    optionElements = $bindable([]),
    onSelect,
    onOptionKeydown,
  }: Props = $props();
</script>

<div
  class={menuClass || defaultMenuClass}
  id={listboxId}
  role="listbox"
  aria-label={listAriaLabel ?? ariaLabel ?? selectedLabel}
  aria-labelledby={ariaLabelledby}
  aria-describedby={ariaDescribedby}
>
  <div class="max-h-60 overflow-y-auto p-2">
    {#each options as option, index (option.value)}
      {@const Icon = option.icon}
      <button
        bind:this={optionElements[index]}
        type="button"
        class="{optionClass || defaultOptionClass} {value === option.value
          ? 'bg-coral/10 ring-coral/20 text-coral ring-1 dark:bg-slate-700 dark:text-slate-50 dark:ring-rose-300/30'
          : 'hover:ring-coral/10 text-stone hover:ring-1 dark:text-slate-300 dark:hover:ring-slate-500/30'}"
        onclick={() => onSelect(option.value)}
        onkeydown={(event) => onOptionKeydown(event, index)}
        role="option"
        tabindex={highlightedIndex === index ? 0 : -1}
        aria-selected={value === option.value}
      >
        {#if Icon}
          <Icon size={16} />
        {/if}
        {option.label}
      </button>
    {/each}
  </div>
</div>
