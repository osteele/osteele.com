# Oliver Steele Portfolio Site

This is my personal portfolio site built with [Astro](https://astro.build) and styled with [Tailwind CSS](https://tailwindcss.com).

## 🧞 Commands

All commands are run from the root of the project, from a terminal:

| Command                 | Action                                             |
| :---------------------- | :------------------------------------------------- |
| `bun install`           | Installs dependencies                              |
| `bun run dev`           | Starts local dev server at `localhost:4321`        |
| `bun run build`         | Build your production site to `./dist/`            |
| `bun run preview`       | Preview your build locally, before deploying       |
| `bun run fix`           | Run biome with auto-fix                            |
| `bun run lint`          | Run biome without auto-fix                         |
| `bun run typecheck`     | Run TypeScript type checking                       |
| `bun run test`          | Run tests                                          |

## 🚀 Project Structure

Inside of this Astro project, you'll see the following folders and files:

```
/
├── public/
│   └── images/       # Static assets
├── src/
│   ├── components/   # Astro & React components
│   ├── data/         # Data files
│   │   └── projects.ttl  # Project data in RDF Turtle format
│   ├── layouts/      # Page layouts
│   ├── lib/          # Helper functions
│   ├── pages/        # Page components (routes)
│   └── styles/       # Global styles
└── astro.config.mjs  # Astro configuration
```

## Data Structure

This site uses RDF data in Turtle format to store project information. The data is stored in `src/data/projects.ttl` and loaded via the N3 library. Each project contains:

- Title
- Description
- Repository URL
- Website URL (if available)
- Categories
- Primary Language
- Creation date
- Modification date
- Archive status

Projects are categorized and displayed on multiple pages based on their category tags.

## Features

- 🚀 Fast page loads with static site generation using Astro
- 🌙 Dark mode support
- 📱 Fully responsive design
- 🔧 Tools and software project showcases
- 💾 RDF data storage with dynamic querying
- 📸 Photography section
- 🧩 Dynamic project categorization
- 📝 Static routes with dynamic content generation

## Technical Details

- Built with Astro for high-performance static site generation
- Uses React components within Astro for interactive elements
- Styled with Tailwind CSS for utility-first styling
- TypeScript for type safety
- N3 library for RDF data processing
- Responsive design with mobile-first approach
- Path aliasing with `@/` prefix for cleaner imports