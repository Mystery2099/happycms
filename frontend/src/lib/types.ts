export type ShellRouteKey = 'home' | 'thoughts' | 'create' | 'search' | 'login';

export type ShellRoutes = Record<ShellRouteKey, string>;

import type { Component } from 'svelte';

export type IconComponent = Component<{ size?: number; class?: string }>;

export type ThoughtRecord = {
	id: number;
	title: string;
	author: string;
	category: string;
	moodScore: number;
	thought: string;
	imageUrl: string | null;
	editUrl: string | null;
	deleteUrl: string | null;
};

export type RecentThought = {
	id: number;
	title: string;
	author: string;
	category: string;
	moodScore: number;
	thought: string;
	editUrl: string | null;
};

export type DashboardStats = {
	total: number;
	categories: number;
	highMood: number;
	withImages: number;
};

// Backend: Auth-related types - uncomment when implementing authentication
// export interface AuthUser {
// 	id: number;
// 	name: string;
// 	email: string;
// }

// export interface AuthState {
// 	isLoggedIn: boolean;
// 	user: AuthUser | null;
// }
