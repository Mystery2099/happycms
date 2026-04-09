/** @type {import('prettier').Config} */
const config = {
  plugins: ["prettier-plugin-svelte", "prettier-plugin-tailwindcss"],
  tailwindConfig: "./tailwind.config.js",
  overrides: [
    {
      files: "*.svelte",
      options: {
        parser: "svelte",
      },
    },
  ],
};

export default config;
