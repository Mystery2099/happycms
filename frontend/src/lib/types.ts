import type { Component } from "svelte";

export type IconComponent = Component<{ size?: number; class?: string }>;

export type RouteKey = "home" | "thoughts" | "create" | "search" | "login";

export type Routes = Record<RouteKey, string>;

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

export type ThoughtFormData = {
  title: string;
  author: string;
  category: string;
  mood_score: number;
  thought: string;
  image_path: string;
};

export type ThoughtFormErrors = Partial<
  Record<keyof ThoughtFormData | "form", string>
>;

export type ThoughtMetadata = {
  id: number;
  createdAt: string;
  updatedAt: string;
  category: string;
  moodScore: number;
  createdBy: string | null;
  updatedBy: string | null;
};

export type Quote = {
  author: string;
  quote: string;
  category: string;
};

export type SegmentedOption = {
  value: string;
  label: string;
  title?: string;
  icon?: IconComponent;
};
