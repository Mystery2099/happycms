<script lang="ts">
  interface Props {
    category: string;
    variant?: "muted" | "accent";
  }

  let { category, variant = "muted" }: Props = $props();
</script>

<span
  class={[
    "inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium",
    variant === "accent" ? "bg-coral/10 text-coral" : "bg-mist/50 text-stone",
  ]}
>
  {category}
</span>
