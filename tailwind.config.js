/** @type {import('tailwindcss').Config} */
export default {
	content: ['./*.php', './app/**/*.php', './frontend/src/**/*.{svelte,ts}'],
	darkMode: 'selector',
	theme: {
		extend: {
			colors: {
				// Refined, editorial palette
				canvas: '#faf9f7',
				ink: '#1a1a1a',
				stone: '#57534e',
				mist: '#e7e5e4',
				coral: '#dc5f50',
				moss: '#5c7c66',
				wheat: '#d4a574'
			},
			fontFamily: {
				display: ['Sora', 'system-ui', 'sans-serif'],
				sans: ['Sora', 'system-ui', 'sans-serif']
			},
			fontSize: {
				'display-xl': ['4.5rem', { lineHeight: '1.1', letterSpacing: '-0.02em' }],
				'display-lg': ['3rem', { lineHeight: '1.15', letterSpacing: '-0.01em' }],
				'display-md': ['2rem', { lineHeight: '1.2' }]
			}
		}
	},
	plugins: []
};
