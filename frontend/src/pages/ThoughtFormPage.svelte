<script lang="ts">
  import {
    BadgeX,
    CalendarDays,
    ChevronLeft,
    Hash,
    Image,
    MessageSquareText,
    Pencil,
    Save,
    SmilePlus,
    Tag,
    Type,
    User,
  } from "@lucide/svelte";
  import DisplayHeading from "../components/site/DisplayHeading.svelte";
  import Section from "../components/site/Section.svelte";
  import DropdownField from "../components/site/form/DropdownField.svelte";
  import InputField from "../components/site/form/InputField.svelte";
  import TextareaField from "../components/site/form/TextareaField.svelte";
  import type {
    ThoughtFormData,
    ThoughtFormErrors,
    ThoughtMetadata,
  } from "../lib/types";

  interface Props {
    mode: "create" | "edit";
    pageLabel: string;
    title: string;
    description: string;
    formAction: string;
    submitLabel: string;
    cancelUrl: string;
    backUrl?: string;
    thoughtData: ThoughtFormData;
    errors: ThoughtFormErrors;
    categories: string[];
    csrfToken: string;
    sideImageUrl: string | null;
    sideImageAlt: string | null;
    metadata: ThoughtMetadata | null;
  }

  let {
    mode,
    pageLabel,
    title,
    description,
    formAction,
    submitLabel,
    cancelUrl,
    backUrl,
    thoughtData,
    errors,
    categories,
    csrfToken,
    sideImageUrl,
    sideImageAlt,
    metadata,
  }: Props = $props();

  const titleErrorId = "thought-title-error";
  const authorErrorId = "thought-author-error";
  const categoryLabelId = "thought-category-label";
  const categoryErrorId = "thought-category-error";
  const moodErrorId = "thought-mood-error";
  const thoughtHintId = "thought-content-hint";
  const thoughtErrorId = "thought-content-error";
  const imageHintId = "thought-image-hint";
  const imageErrorId = "thought-image-error";
</script>

<Section>
  <div>
    {#if backUrl}
      <div class="mb-8">
        <a
          href={backUrl}
          class="inline-flex items-center gap-2 text-sm text-stone transition-colors hover:text-ink"
        >
          <ChevronLeft size={16} />
          Back to collection
        </a>
      </div>
    {/if}

    {#if metadata}
      <div class="mb-8 border-b border-mist pb-6">
        <div
          class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between"
        >
          <div>
            <p class="inline-flex items-center gap-2 text-sm text-stone">
              <Hash size={16} />
              Editing thought #{metadata.id}
            </p>
          </div>
          <span
            class="bg-mist/50 inline-flex items-center rounded-full px-3 py-1 text-xs font-medium text-stone"
          >
            <Tag size={12} class="mr-1.5" />
            {metadata.category} • {"★".repeat(metadata.moodScore)}
          </span>
        </div>

        <div class="mt-5 grid gap-4 md:grid-cols-2">
          <div class="bg-mist/35 rounded-3xl px-5 py-4">
            <p class="inline-flex items-center gap-2 text-sm text-stone">
              <User size={16} />
              Created by
            </p>
            <p class="mt-2 text-base font-medium text-ink">
              {metadata.createdBy ?? "Unknown user"}
            </p>
            <p class="mt-2 inline-flex items-center gap-2 text-sm text-stone">
              <CalendarDays size={16} />
              {metadata.createdAt}
            </p>
          </div>

          <div class="bg-mist/35 rounded-3xl px-5 py-4">
            <p class="inline-flex items-center gap-2 text-sm text-stone">
              <Pencil size={16} />
              Last edited by
            </p>
            <p class="mt-2 text-base font-medium text-ink">
              {metadata.updatedBy ?? "Unknown user"}
            </p>
            <p class="mt-2 inline-flex items-center gap-2 text-sm text-stone">
              <CalendarDays size={16} />
              {metadata.updatedAt}
            </p>
          </div>
        </div>
      </div>
    {/if}

    <div class="grid gap-12 lg:grid-cols-[1fr_2fr] lg:gap-16">
      <aside>
        <DisplayHeading
          level="h1"
          eyebrow={pageLabel}
          {title}
          {description}
          icon={mode === "edit" ? Pencil : undefined}
          className="mb-8"
        />

        {#if sideImageUrl}
          <div
            class={[
              "bg-mist/30 hidden items-center justify-center overflow-hidden lg:flex",
              mode === "create" ? "aspect-square" : "aspect-[4/3]",
            ]}
          >
            <img
              src={sideImageUrl}
              alt={sideImageAlt ?? ""}
              class={[
                "object-center",
                mode === "create"
                  ? "h-3/4 w-3/4 object-contain"
                  : "h-full w-full object-cover",
              ]}
            />
          </div>
        {/if}
      </aside>

      <div class="border-mist lg:border-l lg:pl-12">
        <form method="post" action={formAction} class="max-w-2xl">
          <input type="hidden" name="csrf_token" value={csrfToken} />

          <div class="space-y-8">
            {#if errors.form}
              <div
                class="border-coral/20 bg-coral/10 border px-4 py-3 text-sm text-coral"
                role="alert"
              >
                {errors.form}
              </div>
            {/if}

            <div class="grid gap-8 md:grid-cols-2">
              <InputField
                id="thought-title"
                name="title"
                label="Title"
                icon={Type}
                value={thoughtData.title}
                placeholder="Give your thought a title"
                required={true}
                minlength={3}
                maxlength={80}
                error={errors.title}
                errorId={titleErrorId}
              />

              <InputField
                id="thought-author"
                name="author"
                label="Author"
                icon={User}
                value={thoughtData.author}
                placeholder="Your name"
                required={true}
                minlength={2}
                maxlength={60}
                error={errors.author}
                errorId={authorErrorId}
              />
            </div>

            <div class="grid gap-8 md:grid-cols-2">
              <DropdownField
                name="category"
                label="Category"
                labelId={categoryLabelId}
                options={categories}
                selected={thoughtData.category}
                icon={Tag}
                error={errors.category}
                errorId={categoryErrorId}
              />

              <InputField
                id="thought-mood"
                name="mood_score"
                type="number"
                label="Mood Score (1-5)"
                icon={SmilePlus}
                value={thoughtData.mood_score}
                min={1}
                max={5}
                error={errors.mood_score}
                errorId={moodErrorId}
              />
            </div>

            <TextareaField
              id="thought-content"
              name="thought"
              label="Your Happy Thought"
              icon={MessageSquareText}
              value={thoughtData.thought}
              placeholder="Share what made you happy..."
              required={true}
              rows={5}
              minlength={12}
              maxlength={400}
              hint="Minimum 12 characters, maximum 400"
              hintId={thoughtHintId}
              error={errors.thought}
              errorId={thoughtErrorId}
            />

            <InputField
              id="thought-image"
              name="image_path"
              label="Image Path (Optional)"
              icon={Image}
              value={thoughtData.image_path}
              placeholder="public/images/spring-hero.jpg"
              hint="Optional. Use a local image path from this project."
              hintId={imageHintId}
              error={errors.image_path}
              errorId={imageErrorId}
            />

            <div class="flex flex-wrap gap-4 pt-4">
              <button type="submit" class="btn-primary">
                <Save size={16} />
                {submitLabel}
              </button>
              <a href={cancelUrl} class="btn-secondary">
                <BadgeX size={16} />
                Cancel
              </a>
            </div>
          </div>
        </form>
      </div>
    </div>
  </div>
</Section>
