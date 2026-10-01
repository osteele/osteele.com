# osteele.com Site Organization

This document describes the site's navigation model, how project pages are
organized, and how a project finds its way onto them.

## Top-level information architecture

The two navigation bars have different scopes:

- The shared cross-site bar links the main site, Notes, Photos, Visualizations,
  and Papers. Those destinations are content collections with their own sibling
  repositories and deployments.
- The local navigation organizes `osteele.com`: Work, Software, Teaching, and
  About. Its canonical data lives in `src/data/navigation.ts`, which feeds both
  the desktop and mobile menus.

The primary routes have distinct jobs:

| Route | Purpose |
| --- | --- |
| `/work` | Curated overview of research, products, current software, teaching, and creative practice |
| `/software` | Detailed inventory of software projects, organized by form factor and use |
| `/teaching` | Gateway to courses and educational materials |
| `/about` | Biography, experience, interests, and contact path |
| `/topics` | Secondary cross-cutting browse facet that joins software, teaching, and creative work by subject |

`/work` should remain selective. It points into the detailed collections but
does not reproduce their taxonomies. `/software` is where the full project
inventory and recently updated software belong. `/topics` is linked from those
contexts rather than promoted as a fifth primary navigation item.

## The model

Projects are not filed into a single place. Each project in `src/data/projects.ttl`
carries several categories, and pages select across two crossing axes:

- **Form factor** — what shape the thing is: `/software/web-apps`,
  `/software/desktop-apps`, `/software/command-line`, `/software/libraries`
- **Domain, platform, and audience** — what it's about and what it plugs into:
  `/software/development-tools`, `/software/agent-tools`,
  `/software/academic-research-tools`, `/software/obsidian`,
  `/topics/language-learning`, `/topics/p5js`, `/topics/physical-computing`,
  `/topics/embroidery`, `/topics/computer-education`

A single project appears on as many pages as it has categories for. Gojekyll is a
CLI *and* a web-publishing tool, and shows up under both.

Within a page, sections group the projects further — and there a project should
appear exactly once. `src/lib/project-categorization.test.ts` and
`src/lib/project-duplicates.test.ts` enforce that: sections that overlap render
the same project twice, and a project matching no section falls into a catch-all
group that is a safety net rather than a home.

## Where the pieces live

| Concern | File |
| --- | --- |
| Project records | `src/data/projects.ttl` |
| Category vocabulary and placement rule | `src/data/categories.ts` |
| Section definitions for every page | `src/data/sections.ts` |
| Section-to-project matching | `src/lib/sections.ts` |
| Shared display helpers (labels, recency) | `src/lib/project-display.ts` |
| Page routes | `src/pages/software/`, `src/pages/topics/` |

Section definitions belong in `src/data/sections.ts`, not inline in a page. The
vocabulary tests enumerate the exported arrays in that module, so a section
defined inside an `.astro` file escapes the check that it still matches projects.

## Page structure

`/software` is a hub of category tiles in three groups — main form factors,
professional tools (development, coding agents, and academic research),
ecosystem tools (p5.js, Obsidian) — plus application domains that link into
`/topics/`. It ends with a Recent Projects block, which is generated from
`dateModified` and excludes archived projects; there is nothing to hand-update
there.

Each linked page renders `CategoryLayout` with a section list and a project
filter. `ProjectList` groups the filtered projects by section and collects
whatever matched none of them into an "Other Projects" group.

## Adding a project

See [projects-data-format.md](projects-data-format.md) for the record format, the
category vocabulary, and the placement rule. In short: assign categories from the
closed vocabulary, then run `mise run check` — the tests report a project that
landed where no page shows it, a section that stopped matching anything, and a
category that no longer exists.

## Adding a page

1. Add a section array to `src/data/sections.ts`, or extend an existing one.
2. Add the route under `src/pages/software/` or `src/pages/topics/`, rendering
   `CategoryLayout` with those sections and a project filter.
3. Link it from the appropriate tile group in `src/pages/software.astro`.
4. Run `mise run check`.
