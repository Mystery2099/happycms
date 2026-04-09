<script lang="ts">
	import { AlertTriangle, BadgeX, Trash2 } from '@lucide/svelte';
	import DisplayHeading from '../components/site/DisplayHeading.svelte';
	import Section from '../components/site/Section.svelte';
	import ThoughtMood from '../components/thoughts/ThoughtMood.svelte';

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
			class="bg-coral/10 border-coral mb-8 border-l-4 p-4"
			role="alert"
			aria-live="assertive"
			aria-atomic="true"
		>
			<div class="flex items-start gap-3">
				<AlertTriangle size={20} class="text-coral mt-0.5 shrink-0" />
				<div>
					<p class="text-ink font-medium">This action cannot be undone</p>
					<p class="text-stone text-sm">
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

		<div class="border-mist bg-wheat/5 mb-8 border p-8">
			<p class="font-display text-xl sm:text-2xl text-ink mb-2">{thought.title}</p>
			<p class="text-stone mb-4">By {thought.author} • {thought.category}</p>
			<p class="text-stone leading-relaxed">{thought.thought}</p>
			<div class="border-mist mt-4 border-t pt-4">
				<ThoughtMood score={thought.moodScore} />
			</div>
		</div>

		<form method="post" action={formAction} class="flex flex-wrap justify-center gap-4">
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
