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
	import FeatureList from '../components/home/FeatureList.svelte';
	import QuickLinks from '../components/home/QuickLinks.svelte';
	import DisplayHeading from '../components/site/DisplayHeading.svelte';
	import Section from '../components/site/Section.svelte';
	import RecentThoughts from '../components/thoughts/RecentThoughts.svelte';
	import type { DashboardStats, RecentThought } from '../lib/types';

	type Routes = {
		create: string | null;
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

	const featureItems = [
		{
			number: '01',
			title: 'Template System',
			description: 'PHP layout includes with a Svelte-rendered shell for consistent pages',
			icon: FileText
		},
		{
			number: '02',
			title: 'HTML Structure',
			description: 'Tables, headings, images, navigation, and semantic page structure',
			icon: Type
		},
		{
			number: '03',
			title: 'Server-Side Processing',
			description: 'Form handling, validation, and database operations',
			icon: Save
		},
		{
			number: '04',
			title: 'AJAX Integration',
			description: 'Fetch API for loading famous quotes dynamically',
			icon: Search
		}
	];

	const quickLinks = $derived.by(() => [
		{ href: routes.create, label: 'Create a new record', icon: PlusCircle },
		{ href: routes.search, label: 'Search the database', icon: Search },
		{ href: routes.thoughts, label: 'Manage all records', icon: FileText }
	]);
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
					{#if routes.create}
						<a href={routes.create} class="btn-primary">
							<PlusCircle size={16} />
							Add a Happy Thought
						</a>
					{/if}
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

<Section>
	<div>
		<div class="mb-8 flex flex-col items-start gap-3 sm:flex-row sm:items-end sm:justify-between sm:gap-4">
			<div>
				<DisplayHeading
					title="Recent Entries"
					description="The latest happy thoughts from our collection"
					className="space-y-0"
					titleClass="mb-2"
				/>
			</div>
			<a href={routes.thoughts} class="editorial-link inline-flex items-center gap-2 text-sm">
				View all thoughts
				<ArrowRight size={16} />
			</a>
		</div>

		<RecentThoughts thoughts={recentThoughts} />
	</div>
</Section>

<Section sectionClass="border-t border-mist bg-white">
	<div>
		<div class="grid gap-16 lg:grid-cols-2">
			<div>
				<DisplayHeading title="What This Site Demonstrates" className="mb-6" />
				<FeatureList items={featureItems} />
			</div>

			<div>
				<DisplayHeading title="Quick Links" className="mb-6" />
				<QuickLinks items={quickLinks} />
			</div>
		</div>
	</div>
</Section>

<Section sectionClass="border-t border-mist">
	<FamousThoughts {apiUrl} />
</Section>
