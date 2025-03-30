/** @type {import('tailwindcss').Config} */
export default {
  content: ['./src/**/*.{astro,html,js,jsx,md,mdx,svelte,ts,tsx,vue}'],
  safelist: [
    // Safelist the color variations we're using dynamically
    {
      pattern: /from-(amber|rose|sky|pink|blue|purple|green|teal)-[0-9]+/,
    },
    {
      pattern: /to-(amber|rose|sky|pink|blue|purple|green|teal)-[0-9]+/,
    },
    {
      pattern: /text-(amber|rose|sky|pink|blue|purple|green|teal)-[0-9]+/,
    },
    {
      pattern: /border-(amber|rose|sky|pink|blue|purple|green|teal)-[0-9]+/,
    },
  ],
  theme: {
    extend: {
      backgroundImage: {
        "gradient-radial": "radial-gradient(var(--tw-gradient-stops))",
        "gradient-conic":
          "conic-gradient(from 180deg at 50% 50%, var(--tw-gradient-stops))",
      },
      fontFamily: {
        serif: ["Georgia", "Cambria", '"Times New Roman"', "Times", "serif"],
      },
    },
    container: {
      center: true,
      padding: {
        DEFAULT: "1rem",
        sm: "2rem",
      },
      screens: {
        sm: "640px",
        md: "768px",
        lg: "1024px",
        xl: "1024px",
        "2xl": "1024px",
      },
    },
  },
  darkMode: "class",
}