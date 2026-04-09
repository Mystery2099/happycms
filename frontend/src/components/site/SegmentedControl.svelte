<script lang="ts">
  import { onMount } from "svelte";
  import type { IconComponent } from "../../lib/types";

  type SegmentedOption = {
    value: string;
    label: string;
    title?: string;
    icon?: IconComponent;
  };

  interface Props {
    options: SegmentedOption[];
    value: string;
    ariaLabel: string;
    groupClass?: string;
    buttonClass?: string;
    onChange?: (value: string) => void;
  }

  let {
    options,
    value = $bindable(),
    ariaLabel,
    groupClass = "",
    buttonClass = "px-3 py-1.5",
    onChange,
  }: Props = $props();

  let isInitialized = $state(false);

  let activeIndex = $derived(
    options.findIndex((option) => option.value === value),
  );
  let activeStyle = $derived.by(() => {
    if (activeIndex < 0) return "opacity: 0;";

    const percent = 100 / options.length;
    const translateX = activeIndex * 100;
    const opacity = isInitialized ? "" : "opacity: 0;";
    return `width: ${percent}%; transform: translateX(${translateX}%); ${opacity}`;
  });

  function selectOption(nextValue: string) {
    value = nextValue;
    onChange?.(nextValue);
  }

  onMount(() => {
    requestAnimationFrame(() => {
      isInitialized = true;
    });
  });
</script>

<div
  class={[
    "bg-mist/50 relative rounded-lg p-0.5 dark:bg-slate-800/50",
    groupClass,
  ]}
  role="group"
  aria-label={ariaLabel}
  style:--segments={options.length}
>
  <div class="absolute inset-0 p-0.5" aria-hidden="true">
    <div
      class="h-full rounded-md bg-white shadow-sm ring-2 ring-coral ring-offset-1 transition-[transform,width] duration-200 ease-enter motion-reduce:transition-none dark:bg-slate-700 dark:ring-offset-slate-800"
      style={activeStyle}
    ></div>
  </div>

  <div
    class="relative grid items-center"
    style:grid-template-columns={`repeat(${options.length}, minmax(0, 1fr))`}
  >
    {#each options as option (option.value)}
      {@const Icon = option.icon}
      <button
        type="button"
        class={[
          "z-10 flex min-w-0 items-center justify-center gap-2 rounded-md text-sm font-medium text-stone transition-colors duration-200 active:scale-[0.97] motion-reduce:transition-none",
          buttonClass,
          value === option.value
            ? "text-ink hover:bg-transparent"
            : "hover:bg-coral-soft hover:text-ink dark:hover:bg-white/10 dark:hover:text-slate-50",
        ]}
        onclick={() => selectOption(option.value)}
        aria-pressed={value === option.value}
        title={option.title ?? option.label}
      >
        {#if Icon}
          <Icon size={14} />
        {/if}
        <span class="truncate">{option.label}</span>
      </button>
    {/each}
  </div>
</div>
