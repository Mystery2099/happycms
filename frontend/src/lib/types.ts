export type ShellRouteKey = 'home' | 'thoughts' | 'create' | 'search' | 'login';

export type ShellRoutes = Record<ShellRouteKey, string>;

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
