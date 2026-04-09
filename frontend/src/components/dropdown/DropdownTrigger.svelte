<script lang="ts">
  import { ChevronDown } from "@lucide/svelte";
  import type { Component } from "svelte";
  import { desktopSurfaceTriggerOpenClass } from "../../lib/ui-classes";

  interface Props {
    isOpen: boolean;
    isMobile: boolean;
    triggerId: string;
    listboxId: string;
    ariaLabel?: string;
    ariaLabelledby?: string;
    ariaDescribedby?: string;
    showIconInTrigger: boolean;
    showLabelInTrigger: boolean;
    selectedLabel: string;
    selectedIcon?: Component;
    triggerClass?: string;
    defaultTriggerClass: string;
    triggerElement?: HTMLButtonElement | null;
    onclick?: () => void;
    onkeydown?: (event: KeyboardEvent) => void;
  }

  let {
    isOpen,
    isMobile,
    triggerId,
    listboxId,
    ariaLabel,
    ariaLabelledby,
    ariaDescribedby,
    showIconInTrigger,
    showLabelInTrigger,
    selectedLabel,
    selectedIcon,
    triggerClass = "",
    defaultTriggerClass,
    triggerElement = $bindable(),
    onclick,
    onkeydown,
  }: Props = $props();

  const SelectedIcon = $derived(selectedIcon);
</script>

<button
  bind:this={triggerElement}
  type="button"
  class={[
    triggerClass || defaultTriggerClass,
    isOpen ? desktopSurfaceTriggerOpenClass : "",
  ]}
  {onclick}
  {onkeydown}
  id={triggerId}
  aria-controls={isOpen ? listboxId : undefined}
  aria-describedby={ariaDescribedby}
  aria-expanded={isOpen}
  aria-haspopup={isMobile ? "dialog" : "listbox"}
  aria-label={ariaLabel}
  aria-labelledby={ariaLabelledby}
>
  <span class="flex min-w-0 items-center gap-2">
    {#if showIconInTrigger && SelectedIcon}
      <SelectedIcon size={16} />
    {/if}
    {#if showLabelInTrigger}
      <span class="truncate capitalize">{selectedLabel}</span>
    {/if}
  </span>
  <ChevronDown
    size={16}
    class="shrink-0 transition-transform duration-200 {isOpen
      ? 'rotate-180'
      : ''}"
  />
</button>
