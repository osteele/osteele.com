/**
 * The canonical project category vocabulary.
 *
 * Categories are the site's only navigation primitive: a project surfaces on a
 * page because one of its categories matches a section in `sections.ts`. That
 * makes the vocabulary load-bearing, and an open vocabulary lets it rot in two
 * directions at once — a project invents a category no section looks for, or a
 * section filters on a category no project carries. Either way a page quietly
 * renders empty.
 *
 * `src/lib/category-vocabulary.test.ts` closes both directions against the lists
 * below, so drift fails `mise run check` instead of the page.
 *
 * ## Placement rule
 *
 * A project carries:
 *
 * 1. exactly one FORM_FACTOR category — the shape of the thing;
 * 2. a DOMAIN category for each subject it belongs to;
 * 3. a PLATFORM category when it plugs into a specific host application;
 * 4. AUDIENCE and LIFECYCLE categories as needed.
 *
 * Do not invent a category for a single project. A category earns its place by
 * covering at least two projects or by being the filter for a section; anything
 * else should widen an existing category or fall through to the form factor.
 * `DERIVED_CATEGORIES` are added by `normalizeCategories` and the language
 * enhancement in `projects.ts` — never author them in `projects.ttl`.
 */

/** The shape of the thing. Every project carries exactly one. */
export const FORM_FACTOR_CATEGORIES = ["cli", "desktop-app", "library", "mcp-server", "web-app"] as const;

/** The host application or framework a project extends. */
export const PLATFORM_CATEGORIES = [
	"obsidian",
	"obsidian-integration",
	"obsidian-plugin",
	"openlaszlo",
	"p5js",
	"p5js-tools",
	"rails-plugins",
	"raycast",
] as const;

/** Subject matter. A project may carry several. */
export const DOMAIN_CATEGORIES = [
	"ai-tools",
	"art",
	"development-tools",
	"document-management",
	"document-processing",
	"language-detection",
	"language-learning",
	"llm-tools",
	"machine-embroidery",
	"music-tools",
	"natural-language-processing",
	"pdf-tools",
	"physical-computing",
	"productivity",
	"programming-visualizations",
	"prompt-matrix",
	"research-computing",
	"research-tools",
	"speech",
	"version-control",
	"web-publishing",
	"web-technologies",
] as const;

/** Who the project is built for. */
export const AUDIENCE_CATEGORIES = ["education", "educator-tools", "student-tools"] as const;

/** Superseded work kept for the record. */
export const LIFECYCLE_CATEGORIES = ["historical-js", "legacy-libraries"] as const;

/**
 * Added during parsing, never written in `projects.ttl`:
 * `webapp` and `library` come from `normalizeCategories`; the language-specific
 * library variants come from `os:topics` and `os:primaryLanguage`.
 */
export const DERIVED_CATEGORIES = [
	"javascript-library",
	"p5-library",
	"python-library",
	"ruby-library",
	"webapp",
] as const;

/** Categories that may appear as an `os:category` value in `projects.ttl`. */
export const AUTHORED_CATEGORIES = [
	...FORM_FACTOR_CATEGORIES,
	...PLATFORM_CATEGORIES,
	...DOMAIN_CATEGORIES,
	...AUDIENCE_CATEGORIES,
	...LIFECYCLE_CATEGORIES,
] as const;

/** Every category a loaded `Project` may carry, or a section may filter on. */
export const KNOWN_CATEGORIES = [...AUTHORED_CATEGORIES, ...DERIVED_CATEGORIES] as const;

export type AuthoredCategory = (typeof AUTHORED_CATEGORIES)[number];
export type KnownCategory = (typeof KNOWN_CATEGORIES)[number];

const authoredSet = new Set<string>(AUTHORED_CATEGORIES);
const knownSet = new Set<string>(KNOWN_CATEGORIES);

export const isAuthoredCategory = (category: string): category is AuthoredCategory => authoredSet.has(category);

export const isKnownCategory = (category: string): category is KnownCategory => knownSet.has(category);
