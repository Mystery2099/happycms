<script lang="ts">
	import { AlertTriangle, BadgeX, Trash2 } from '@lucide/svelte';

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

	function renderMood(score: number): string {
		return '★'.repeat(score);
	}
</script>

<section class="section-padding">
	<div class="max-w-2xl mx-auto px-6 lg:px-8">
		<div class="bg-coral/10 border-coral mb-8 border-l-4 p-4">
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
			<p class="text-coral text-sm font-medium uppercase tracking-widest mb-4 inline-flex items-center gap-2">
				<Trash2 size={16} />
				Delete Confirmation
			</p>
			<h1 class="font-display text-display-md text-ink inline-flex items-center gap-3">
				<AlertTriangle size={24} class="text-coral" />
				Are you sure?
			</h1>
		</div>

		<div class="border-mist bg-wheat/5 mb-8 border p-8">
			<p class="font-display text-2xl text-ink mb-2">{thought.title}</p>
			<p class="text-stone mb-4">By {thought.author} • {thought.category}</p>
			<p class="text-stone leading-relaxed">{thought.thought}</p>
			<div class="border-mist mt-4 border-t pt-4">
				<span class="text-wheat">{renderMood(thought.moodScore)}</span>
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
</section>
