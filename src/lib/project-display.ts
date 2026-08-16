import type { Project } from "@/data/projects.types";

/**
 * Display labels for categories that sentence case gets wrong — initialisms,
 * proper nouns, and names shorter than their slug.
 */
const CATEGORY_LABELS: Record<string, string> = {
	"ai-tools": "AI tools",
	cli: "CLI",
	"document-processing": "Documents",
	"historical-js": "Historical JavaScript",
	"javascript-library": "JavaScript library",
	"llm-tools": "LLM tools",
	"mcp-server": "MCP server",
	"music-tools": "Music",
	"natural-language-processing": "NLP",
	obsidian: "Obsidian",
	"obsidian-integration": "Obsidian integration",
	"obsidian-plugin": "Obsidian plugin",
	openlaszlo: "OpenLaszlo",
	p5js: "p5.js",
	"p5-library": "p5.js library",
	"p5js-tools": "p5.js tools",
	"pdf-tools": "PDF tools",
	raycast: "Raycast",
	"rails-plugins": "Rails plugins",
	webapp: "Web app",
};

/** Sentence case: "machine-embroidery" reads as "Machine embroidery". */
const sentenceCase = (slug: string): string => {
	const words = slug.replace(/-/g, " ");
	return words.charAt(0).toUpperCase() + words.slice(1);
};

export const formatCategoryLabel = (category: string): string => CATEGORY_LABELS[category] ?? sentenceCase(category);

export const displayCategoryLabels = (categories: string[], limit = 2): string[] =>
	[...new Set(categories.map(formatCategoryLabel))].slice(0, limit);

/**
 * The most recently updated non-archived projects, newest first.
 *
 * Projects without a `dateModified` are excluded rather than sorted to the end —
 * `mise run update-projects` fills the field from GitHub, so a missing date means
 * the entry hasn't been synced and its position would be arbitrary.
 */
export const recentlyUpdatedProjects = (projects: Project[], limit: number): Project[] =>
	projects
		.filter((project) => !project.isArchived && project.dateModified)
		.sort((a, b) => (b.dateModified?.getTime() ?? 0) - (a.dateModified?.getTime() ?? 0))
		.slice(0, limit);
