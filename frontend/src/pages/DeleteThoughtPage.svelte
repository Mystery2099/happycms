<script lang="ts">
  import { AlertTriangle, BadgeX, Trash2 } from "@lucide/svelte";
  import DisplayHeading from "../components/site/DisplayHeading.svelte";
  import Section from "../components/site/Section.svelte";
  import ThoughtMood from "../components/thoughts/ThoughtMood.svelte";

  type Thought = {
    id: number;
    title: string;
    author: string;
    category: string;
    moodScore: number;
    thought: string;
  };

  interface Props {
    thought: Thought;
    formAction: string;
    cancelUrl: string;
    csrfToken: string;
  }

  let { thought, formAction, cancelUrl, csrfToken }: Props = $props();
</script>

<Section widthClass="max-w-2xl">
  <div>
    <div
      class="bg-coral/10 mb-8 border-l-4 border-coral p-4"
      role="alert"
      aria-live="assertive"
      aria-atomic="true"
    >
      <div class="flex items-start gap-3">
        <AlertTriangle size={20} class="mt-0.5 shrink-0 text-coral" />
        <div>
          <p class="font-medium text-ink">This action cannot be undone</p>
          <p class="text-sm text-stone">
            The thought will be permanently removed from the database.
          </p>
        </div>
      </div>
    </div>

    <div class="mb-12 text-center">
      <DisplayHeading
        level="h1"
        eyebrow="Delete Confirmation"
        title="Are you sure?"
        icon={AlertTriangle}
        className="items-center"
        eyebrowClass="text-coral inline-flex items-center gap-2 justify-center"
      />
    </div>

    <div class="bg-wheat/5 mb-8 border border-mist p-8">
      <p class="mb-2 font-display text-xl text-ink sm:text-2xl">
        {thought.title}
      </p>
      <p class="mb-4 text-stone">By {thought.author} • {thought.category}</p>
      <p class="leading-relaxed text-stone">{thought.thought}</p>
      <div class="mt-4 border-t border-mist pt-4">
        <ThoughtMood score={thought.moodScore} />
      </div>
    </div>

    <form
      method="post"
      action={formAction}
      class="flex flex-wrap justify-center gap-4"
    >
      <input type="hidden" name="csrf_token" value={csrfToken} />
      <a href={cancelUrl} class="btn-secondary">
        <BadgeX size={16} />
        Keep this thought
      </a>
      <button type="submit" class="btn-destructive">
        <Trash2 size={16} />
        Delete permanently
      </button>
    </form>
  </div>
</Section>
