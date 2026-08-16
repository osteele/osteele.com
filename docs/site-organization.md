# osteele.com Site Organization

This document describes how project pages are organized, and how a project finds
its way onto them.

## The model

Projects are not filed into a single place. Each project in `src/data/projects.ttl`
carries several categories, and pages select across two crossing axes:

- **Form factor** — what shape the thing is: `/software/web-apps`,
  `/software/desktop-apps`, `/software/command-line`, `/software/libraries`
- **Domain, platform, and audience** — what it's about and what it plugs into:
  `/software/development-tools`, `/software/academic-research-tools`,
  `/software/obsidian`, `/topics/language-learning`, `/topics/p5js`,
  `/topics/physical-computing`, `/topics/embroidery`, `/topics/computer-education`

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
professional tools (development and academic research), ecosystem tools (p5.js,
Obsidian) — plus application domains that link into `/topics/`. It ends with a
Recent Projects block, which is generated from `dateModified` and excludes
archived projects; there is nothing to hand-update there.

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
