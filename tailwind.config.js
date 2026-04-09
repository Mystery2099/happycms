/** @type {import('tailwindcss').Config} */
export default {
  content: ["./*.php", "./app/**/*.php", "./frontend/src/**/*.{svelte,ts}"],
  darkMode: "selector",
  theme: {
    extend: {
      colors: {
        // Refined, editorial palette - uses CSS variables for dark mode
        canvas: "var(--canvas)",
        "canvas-elevated": "var(--canvas-elevated)",
        ink: "var(--ink)",
        stone: "var(--stone)",
        mist: "var(--mist)",
        coral: "var(--coral)",
        "coral-soft": "var(--coral-soft)",
        moss: "var(--moss)",
        wheat: "var(--wheat)",
      },
      fontFamily: {
        display: ["Sora", "system-ui", "sans-serif"],
        sans: ["Sora", "system-ui", "sans-serif"],
      },
      fontSize: {
        "display-xl": "4.5rem",
        "display-lg": "3rem",
        "display-md": "2rem",
        "display-sm": "1.5rem",
      },
      borderRadius: {
        // Design system radius tokens
        sm: "6px",
        md: "12px",
        lg: "16px",
        xl: "24px",
        sheet: "28px",
      },
      boxShadow: {
        // Design system elevation tokens
        card: "0 2px 8px rgba(15, 23, 42, 0.08)",
        "card-hover": "0 4px 16px rgba(15, 23, 42, 0.12)",
        dropdown: "0 18px 45px rgba(15, 23, 42, 0.12)",
        sheet: "0 -12px 40px rgba(15, 23, 42, 0.18)",
        button: "0 4px 12px rgba(220, 95, 80, 0.2)",
      },
      transitionDuration: {
        150: "150ms",
        200: "200ms",
        250: "250ms",
        300: "300ms",
        400: "400ms",
      },
      transitionTimingFunction: {
        "out-expo": "cubic-bezier(0.16, 1, 0.3, 1)",
        "in-out-smooth": "cubic-bezier(0.4, 0, 0.2, 1)",
        enter: "cubic-bezier(0.22, 1, 0.36, 1)",
        move: "cubic-bezier(0.25, 1, 0.5, 1)",
      },
      keyframes: {
        "dropdown-enter": {
          from: {
            opacity: "0",
            transform: "scaleY(0.88)",
          },
        },
      },
      animation: {
        "dropdown-enter":
          "dropdown-enter 200ms cubic-bezier(0.22, 1, 0.36, 1) both",
      },
    },
  },
  plugins: [],
};
