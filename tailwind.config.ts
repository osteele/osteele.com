import type { Config } from "tailwindcss";

const config: Config = {
  content: [
    "./src/pages/**/*.{js,ts,jsx,tsx,mdx}",
    "./src/components/**/*.{js,ts,jsx,tsx,mdx}",
    "./src/app/**/*.{js,ts,jsx,tsx,mdx}",
  ],
  safelist: [
    // Safelist the color variations we're using dynamically
    {
      pattern: /from-(amber|rose|sky|pink|blue|purple|green)-[0-9]+/,
    },
    {
      pattern: /to-(amber|rose|sky|pink|blue|purple|green)-[0-9]+/,
    },
    {
      pattern: /text-(amber|rose|sky|pink|blue|purple|green)-[0-9]+/,
    },
    {
      pattern: /border-(amber|rose|sky|pink|blue|purple|green)-[0-9]+/,
    },
  ],
  theme: {
    extend: {
      backgroundImage: {
        "gradient-radial": "radial-gradient(var(--tw-gradient-stops))",
        "gradient-conic":
          "conic-gradient(from 180deg at 50% 50%, var(--tw-gradient-stops))",
      },
    },
  },
  darkMode: "class",
};

export default config;