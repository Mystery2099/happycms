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
		User
	} from '@lucide/svelte';
	import FormDropdown from '../components/FormDropdown.svelte';

	type ThoughtFormData = {
		title: string;
		author: string;
		category: string;
		mood_score: number;
		thought: string;
		image_path: string;
	};

	type FormErrors = Partial<Record<keyof ThoughtFormData | 'form', string>>;

	interface Props {
		mode: 'create' | 'edit';
		pageLabel: string;
		title: string;
		description: string;
		formAction: string;
		submitLabel: string;
		cancelUrl: string;
		backUrl?: string;
		thoughtData: ThoughtFormData;
		errors: FormErrors;
		categories: string[];
		csrfToken: string;
		sideImageUrl: string | null;
		sideImageAlt: string | null;
		metadata: { id: number; updatedAt: string; category: string; moodScore: number } | null;
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
		metadata
	}: Props = $props();
</script>

<section class="section-padding">
	<div class="max-w-6xl mx-auto px-6 lg:px-8">
		{#if backUrl}
			<div class="mb-8">
				<a
					href={backUrl}
					class="text-sm text-stone inline-flex items-center gap-2 transition-colors hover:text-ink"
				>
					<ChevronLeft size={16} />
					Back to collection
				</a>
			</div>
		{/if}

		{#if metadata}
			<div class="border-mist mb-8 border-b pb-6">
				<div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
					<div>
						<p class="text-sm text-stone inline-flex items-center gap-2">
							<Hash size={16} />
							Editing thought #{metadata.id}
						</p>
						<p class="text-sm text-stone mt-1 inline-flex items-center gap-2">
							<CalendarDays size={16} />
							Last updated: {metadata.updatedAt}
						</p>
					</div>
					<span
						class="bg-mist/50 text-stone inline-flex items-center rounded-full px-3 py-1 text-xs font-medium"
					>
						<Tag size={12} class="mr-1.5" />
						{metadata.category} • {'★'.repeat(metadata.moodScore)}
					</span>
				</div>
			</div>
		{/if}

		<div class="grid gap-12 lg:grid-cols-[1fr_2fr] lg:gap-16">
			<aside>
				<p class="text-sm font-medium text-stone uppercase tracking-widest mb-4">{pageLabel}</p>
				<h1 class="font-display text-display-md text-ink mb-6 inline-flex items-center gap-3">
					{#if mode === 'edit'}
						<Pencil size={24} class="text-coral" />
					{/if}
					{title}
				</h1>
				<p class="text-stone leading-relaxed mb-8">{description}</p>

				{#if sideImageUrl}
					<div
						class={[
							'overflow-hidden bg-mist/30 flex items-center justify-center',
							mode === 'create' ? 'aspect-square' : 'aspect-[4/3]'
						]}
					>
						<img
							src={sideImageUrl}
							alt={sideImageAlt ?? ''}
							class={[
								'object-center',
								mode === 'create' ? 'h-3/4 w-3/4 object-contain' : 'h-full w-full object-cover'
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
							<div class="border border-coral/20 bg-coral/10 px-4 py-3 text-sm text-coral" role="alert">
								{errors.form}
							</div>
						{/if}

						<div class="grid gap-8 md:grid-cols-2">
							<div>
								<label
									for="thought-title"
									class="text-sm font-medium text-stone mb-2 inline-flex items-center gap-2"
								>
									<Type size={16} />
									Title
								</label>
								<input
									id="thought-title"
									type="text"
									name="title"
									value={thoughtData.title}
									class="input-minimal"
									placeholder="Give your thought a title"
									required
									minlength="3"
									maxlength="80"
									aria-invalid={errors.title ? 'true' : undefined}
								/>
								{#if errors.title}
									<p class="mt-2 text-sm text-coral">{errors.title}</p>
								{/if}
							</div>

							<div>
								<label
									for="thought-author"
									class="text-sm font-medium text-stone mb-2 inline-flex items-center gap-2"
								>
									<User size={16} />
									Author
								</label>
								<input
									id="thought-author"
									type="text"
									name="author"
									value={thoughtData.author}
									class="input-minimal"
									placeholder="Your name"
									required
									minlength="2"
									maxlength="60"
									aria-invalid={errors.author ? 'true' : undefined}
								/>
								{#if errors.author}
									<p class="mt-2 text-sm text-coral">{errors.author}</p>
								{/if}
							</div>
						</div>

						<div class="grid gap-8 md:grid-cols-2">
							<div>
								<label class="text-sm font-medium text-stone mb-2 inline-flex items-center gap-2">
									<Tag size={16} />
									Category
								</label>
								<FormDropdown
									name="category"
									options={categories}
									selected={thoughtData.category}
									ariaLabel="Category"
								/>
								{#if errors.category}
									<p class="mt-2 text-sm text-coral">{errors.category}</p>
								{/if}
							</div>

							<div>
								<label
									for="thought-mood"
									class="text-sm font-medium text-stone mb-2 inline-flex items-center gap-2"
								>
									<SmilePlus size={16} />
									Mood Score (1-5)
								</label>
								<input
									id="thought-mood"
									type="number"
									name="mood_score"
									value={thoughtData.mood_score}
									min="1"
									max="5"
									class="input-minimal"
									aria-invalid={errors.mood_score ? 'true' : undefined}
								/>
								{#if errors.mood_score}
									<p class="mt-2 text-sm text-coral">{errors.mood_score}</p>
								{/if}
							</div>
						</div>

						<div>
							<label
								for="thought-content"
								class="text-sm font-medium text-stone mb-2 inline-flex items-center gap-2"
							>
								<MessageSquareText size={16} />
								Your Happy Thought
							</label>
							<textarea
								id="thought-content"
								name="thought"
								rows="5"
								class="input-minimal resize-none"
								placeholder="Share what made you happy..."
								required
								minlength="12"
								maxlength="400"
								aria-invalid={errors.thought ? 'true' : undefined}
							>{thoughtData.thought}</textarea>
							<p class="mt-2 text-xs text-stone">Minimum 12 characters, maximum 400</p>
							{#if errors.thought}
								<p class="mt-2 text-sm text-coral">{errors.thought}</p>
							{/if}
						</div>

						<div>
							<label
								for="thought-image"
								class="text-sm font-medium text-stone mb-2 inline-flex items-center gap-2"
							>
								<Image size={16} />
								Image Path (Optional)
							</label>
							<input
								id="thought-image"
								type="text"
								name="image_path"
								value={thoughtData.image_path}
								class="input-minimal"
								placeholder="public/images/spring-hero.jpg"
								aria-invalid={errors.image_path ? 'true' : undefined}
							/>
							<p class="mt-2 text-xs text-stone">Optional. Use a local image path from this project.</p>
							{#if errors.image_path}
								<p class="mt-2 text-sm text-coral">{errors.image_path}</p>
							{/if}
						</div>

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
</section>
