import { FileText, Home, PlusCircle, Search } from '@lucide/svelte';
import type { IconComponent } from './types';

export type PrimaryNavRoute = 'home' | 'thoughts' | 'create' | 'search';

export type PrimaryNavItem = {
	key: PrimaryNavRoute;
	label: string;
	icon: IconComponent;
};

export function buildPrimaryNavItems(isAdmin: boolean, mobile = false): PrimaryNavItem[] {
	return [
		{ key: 'home', label: 'Home', icon: Home },
		{ key: 'thoughts', label: 'Thoughts', icon: FileText },
		...(isAdmin ? [{ key: 'create' as const, label: mobile ? 'Add' : 'Add Thought', icon: PlusCircle }] : []),
		{ key: 'search', label: 'Search', icon: Search }
	];
}
