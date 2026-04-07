<script lang="ts">
	import {
		ArrowRight,
		FileText,
		Image,
		PlusCircle,
		Save,
		Search,
		SmilePlus,
		Star,
		Tag,
		Type
	} from '@lucide/svelte';
	import FamousThoughts from '../components/FamousThoughts.svelte';

	type DashboardStats = {
		total: number;
		categories: number;
		highMood: number;
		withImages: number;
	};

	type RecentThought = {
		id: number;
		title: string;
		author: string;
		category: string;
		moodScore: number;
		thought: string;
		editUrl: string | null;
	};

	type Routes = {
		create: string;
		search: string;
		thoughts: string;
	};

	interface Props {
		stats: DashboardStats;
		recentThoughts: RecentThought[];
		routes: Routes;
		heroImageUrl: string;
		apiUrl: string;
	}

	let { stats, recentThoughts, routes, heroImageUrl, apiUrl }: Props = $props();

	function renderMood(score: number): string {
		return '★'.repeat(score);
	}
</script>

<section class="border-b border-mist">
	<div class="max-w-6xl mx-auto px-6 py-16 lg:px-8 lg:py-24">
		<div class="grid items-center gap-12 lg:grid-cols-2 lg:gap-16">
			<div>
				<p class="text-sm font-medium uppercase tracking-widest text-stone mb-4">CISY 7203</p>
				<h1 class="font-display text-display-lg text-ink mb-6">
					A collection of happy thoughts for brighter days.
				</h1>
				<p class="text-stone leading-relaxed mb-8 max-w-lg">
					This content management system demonstrates PHP, SQLite, Svelte, and modern web
					development. Create, browse, and search through moments of joy.
				</p>
				<div class="flex flex-wrap gap-4">
					<a href={routes.create} class="btn-primary">
						<PlusCircle size={16} />
						Add a Happy Thought
					</a>
					<a href={routes.thoughts} class="btn-secondary">
						<FileText size={16} />
						Browse Collection
					</a>
				</div>
			</div>

			<div class="relative">
				<img
					src={heroImageUrl}
					alt="Spring flowers in warm light"
					class="aspect-[4/3] w-full object-cover"
				/>
				<div class="absolute -bottom-4 -left-4 hidden bg-white p-4 shadow-lg lg:block">
					<SmilePlus size={20} class="text-coral mb-2" />
					<p class="font-display text-2xl text-ink">{stats.total}</p>
					<p class="text-sm text-stone">Happy thoughts collected</p>
				</div>
			</div>
		</div>
	</div>
</section>

<section class="border-b border-mist bg-white">
	<div class="max-w-6xl mx-auto px-6 lg:px-8">
		<div class="grid grid-cols-2 gap-px bg-mist md:grid-cols-4 md:gap-0 md:bg-transparent md:divide-x md:divide-mist">
			<div class="bg-white px-4 py-7 text-center md:bg-transparent md:py-8">
				<FileText size={20} class="text-coral mx-auto mb-2" />
				<p class="font-display text-3xl text-ink">{stats.total}</p>
				<p class="text-sm text-stone mt-1">Total Records</p>
			</div>
			<div class="bg-white px-4 py-7 text-center md:bg-transparent md:py-8">
				<Tag size={20} class="text-coral mx-auto mb-2" />
				<p class="font-display text-3xl text-ink">{stats.categories}</p>
				<p class="text-sm text-stone mt-1">Categories</p>
			</div>
			<div class="bg-white px-4 py-7 text-center md:bg-transparent md:py-8">
				<Star size={20} class="text-coral mx-auto mb-2" />
				<p class="font-display text-3xl text-ink">{stats.highMood}</p>
				<p class="text-sm text-stone mt-1">High Mood (4-5★)</p>
			</div>
			<div class="bg-white px-4 py-7 text-center md:bg-transparent md:py-8">
				<Image size={20} class="text-coral mx-auto mb-2" />
				<p class="font-display text-3xl text-ink">{stats.withImages}</p>
				<p class="text-sm text-stone mt-1">With Images</p>
			</div>
		</div>
	</div>
</section>

<section class="section-padding">
	<div class="max-w-6xl mx-auto px-6 lg:px-8">
		<div class="mb-8 flex flex-col items-start gap-3 sm:flex-row sm:items-end sm:justify-between sm:gap-4">
			<div>
				<h2 class="font-display text-display-md text-ink mb-2">Recent Entries</h2>
				<p class="text-stone">The latest happy thoughts from our collection</p>
			</div>
			<a href={routes.thoughts} class="editorial-link inline-flex items-center gap-2 text-sm">
				View all thoughts
				<ArrowRight size={16} />
			</a>
		</div>

		<div class="border border-mist">
			<div class="md:hidden">
				<div class="divide-y divide-mist">
					{#each recentThoughts as thought (thought.id)}
						<article class="space-y-4 p-5">
							<div class="flex items-start justify-between gap-3">
								<div class="min-w-0">
									{#if thought.editUrl}
										<a
											href={thought.editUrl}
											class="font-display text-xl leading-tight text-ink transition-colors hover:text-coral"
										>
											{thought.title}
										</a>
									{:else}
										<p class="font-display text-xl leading-tight text-ink">{thought.title}</p>
									{/if}
									<p class="mt-2 text-sm leading-relaxed text-stone line-clamp-2">
										{thought.thought}
									</p>
								</div>
								<span
									class="bg-mist/50 text-stone inline-flex shrink-0 items-center px-2.5 py-0.5 text-xs font-medium"
								>
									{thought.category}
								</span>
							</div>

							<div class="flex items-center justify-between gap-3 border-t border-mist pt-3">
								<p class="text-sm font-medium text-stone">{thought.author}</p>
								<p class="text-sm text-wheat">{renderMood(thought.moodScore)}</p>
							</div>
						</article>
					{/each}
				</div>
			</div>

			<div class="hidden overflow-hidden md:block">
				<table class="data-table">
					<caption class="sr-only">Recent happy thoughts from the database</caption>
					<thead>
						<tr>
							<th>Title</th>
							<th>Author</th>
							<th>Category</th>
							<th class="text-right">Mood</th>
						</tr>
					</thead>
					<tbody>
						{#each recentThoughts as thought (thought.id)}
							<tr>
								<td>
									{#if thought.editUrl}
										<a
											href={thought.editUrl}
											class="font-medium text-ink transition-colors hover:text-coral"
										>
											{thought.title}
										</a>
									{:else}
										<p class="font-medium text-ink">{thought.title}</p>
									{/if}
									<p class="text-sm text-stone line-clamp-1 mt-1">{thought.thought}</p>
								</td>
								<td class="text-stone">{thought.author}</td>
								<td>
									<span
										class="bg-mist/50 text-stone inline-flex items-center px-2.5 py-0.5 text-xs font-medium"
									>
										{thought.category}
									</span>
								</td>
								<td class="text-right text-wheat">{renderMood(thought.moodScore)}</td>
							</tr>
						{/each}
					</tbody>
				</table>
			</div>
		</div>
	</div>
</section>

<section class="section-padding border-t border-mist bg-white">
	<div class="max-w-6xl mx-auto px-6 lg:px-8">
		<div class="grid gap-16 lg:grid-cols-2">
			<div>
				<h2 class="font-display text-display-md text-ink mb-6">What This Site Demonstrates</h2>
				<div class="space-y-4">
					<div class="flex gap-4">
						<span class="text-coral inline-flex items-center gap-2 font-medium">
							<FileText size={16} />
							01
						</span>
						<div>
							<p class="font-medium text-ink">Template System</p>
							<p class="text-sm text-stone">
								PHP layout includes with a Svelte-rendered shell for consistent pages
							</p>
						</div>
					</div>
					<div class="flex gap-4">
						<span class="text-coral inline-flex items-center gap-2 font-medium">
							<Type size={16} />
							02
						</span>
						<div>
							<p class="font-medium text-ink">HTML Structure</p>
							<p class="text-sm text-stone">
								Tables, headings, images, navigation, and semantic page structure
							</p>
						</div>
					</div>
					<div class="flex gap-4">
						<span class="text-coral inline-flex items-center gap-2 font-medium">
							<Save size={16} />
							03
						</span>
						<div>
							<p class="font-medium text-ink">Server-Side Processing</p>
							<p class="text-sm text-stone">
								Form handling, validation, and database operations
							</p>
						</div>
					</div>
					<div class="flex gap-4">
						<span class="text-coral inline-flex items-center gap-2 font-medium">
							<Search size={16} />
							04
						</span>
						<div>
							<p class="font-medium text-ink">AJAX Integration</p>
							<p class="text-sm text-stone">
								Fetch API for loading famous quotes dynamically
							</p>
						</div>
					</div>
				</div>
			</div>

			<div>
				<h2 class="font-display text-display-md text-ink mb-6">Quick Links</h2>
				<nav class="space-y-3" aria-label="Primary actions">
					<a
						href={routes.create}
						class="group border-mist flex items-center justify-between border-b py-3"
					>
						<span
							class="text-ink inline-flex items-center gap-3 transition-colors group-hover:text-coral"
						>
							<PlusCircle size={16} />
							Create a new record
						</span>
						<ArrowRight size={16} class="text-stone" />
					</a>
					<a
						href={routes.search}
						class="group border-mist flex items-center justify-between border-b py-3"
					>
						<span
							class="text-ink inline-flex items-center gap-3 transition-colors group-hover:text-coral"
						>
							<Search size={16} />
							Search the database
						</span>
						<ArrowRight size={16} class="text-stone" />
					</a>
					<a
						href={routes.thoughts}
						class="group border-mist flex items-center justify-between border-b py-3"
					>
						<span
							class="text-ink inline-flex items-center gap-3 transition-colors group-hover:text-coral"
						>
							<FileText size={16} />
							Manage all records
						</span>
						<ArrowRight size={16} class="text-stone" />
					</a>
				</nav>
			</div>
		</div>
	</div>
</section>

<section class="section-padding border-t border-mist">
	<div class="max-w-6xl mx-auto px-6 lg:px-8">
		<FamousThoughts {apiUrl} />
	</div>
</section>
