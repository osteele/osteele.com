import type { Section } from "@/lib/sections";

/**
 * Section definitions for the project listing pages.
 *
 * Every category named here must exist in `@/data/categories`, and every section
 * must match at least one project; `src/lib/category-vocabulary.test.ts` enforces
 * both, so a section can't quietly stop matching anything.
 */

export const WebAppSections: Section[] = [
	{
		id: "software-development",
		name: "Software Development",
		color: "from-amber-500",
		titleColor: "from-amber-500 to-amber-300",
		description: "Web applications for development workflows and code generation.",
		categories: ["development-tools", "web-technologies"],
		subsections: [{ name: "Web Publishing", categories: ["web-publishing"] }],
	},
	{
		id: "language-learning",
		name: "Language Learning",
		color: "from-sky-500",
		titleColor: "from-sky-500 to-sky-300",
		description: "Web applications to assist in learning foreign languages.",
		categories: ["language-learning"],
	},
	{
		id: "llm-tools",
		name: "LLM Applications",
		color: "from-rose-500",
		titleColor: "from-rose-500 to-rose-300",
		description: "Web interfaces for working with large language models and their outputs.",
		categories: ["llm-tools"],
	},
	{
		id: "machine-embroidery",
		name: "Machine Embroidery",
		color: "from-pink-500",
		titleColor: "from-pink-500 to-pink-300",
		description: "Web applications for machine embroidery design and file conversion.",
		categories: ["machine-embroidery"],
	},
	{
		id: "music",
		name: "Music",
		color: "from-orange-500",
		titleColor: "from-orange-500 to-orange-300",
		description: "Web applications for music theory, scales, and instrument fingerings.",
		categories: ["music-tools"],
	},
	{
		id: "p5js",
		name: "p5.js Web Apps",
		color: "from-blue-500",
		titleColor: "from-blue-500 to-blue-300",
		description: "Web-based tools for the p5.js creative coding framework.",
		categories: ["p5js"],
	},
	{
		id: "physical-computing",
		name: "Physical Computing",
		color: "from-purple-500",
		titleColor: "from-purple-500 to-purple-300",
		description: "Web applications for working with microcontrollers and sensor data.",
		categories: ["physical-computing"],
	},
	{
		id: "computer-education",
		name: "Computer Education",
		color: "from-green-500",
		titleColor: "from-green-500 to-green-300",
		description: "Web applications for computer science education and visualization.",
		// Not the broad `education` audience category: that also covers subject
		// teaching (music, language), which belongs under its own domain section.
		categories: ["student-tools", "programming-visualizations"],
	},
	{
		id: "art-projects",
		name: "Art Projects",
		color: "from-violet-500",
		titleColor: "from-violet-500 to-violet-300",
		description: "Interactive web-based art projects and visualizations.",
		categories: ["art"],
	},
];

export const EducationalSoftwareSections: Section[] = [
	{
		id: "programming-visualizations",
		name: "Programming Visualizations",
		color: "from-blue-500",
		titleColor: "from-blue-500 to-blue-300",
		description: "Interactive visualizations to aid in learning programming concepts.",
		categories: ["programming-visualizations"],
	},
	{
		id: "educator-tools",
		name: "Educator Tools",
		color: "from-teal-500",
		titleColor: "from-teal-500 to-teal-300",
		description: "Software designed for educators in technical subjects.",
		categories: ["educator-tools"],
	},
	{
		id: "physical-computing-education",
		name: "Physical Computing",
		color: "from-purple-500",
		titleColor: "from-purple-500 to-purple-300",
		description: "Educational tools for physical computing and electronics.",
		categories: ["physical-computing"],
	},
];

export const SoftwareSections: Section[] = [
	{
		id: "web-tools",
		name: "Web & Publishing",
		color: "from-amber-500",
		titleColor: "from-amber-500 to-amber-300",
		description: "Web Publishing & Documentation",
		categories: ["web-publishing", "web-technologies"],
	},
	{
		id: "command-line",
		name: "Command Line Tools",
		color: "from-green-500",
		titleColor: "from-green-500 to-green-300",
		description: "Terminal-based utilities and tools",
		categories: ["cli"],
	},
	{
		id: "libraries",
		name: "Libraries & Frameworks",
		color: "from-indigo-500",
		titleColor: "from-indigo-500 to-indigo-300",
		description: "Code libraries for developers",
		categories: ["library"],
	},
	{
		id: "language-learning",
		name: "Language Learning",
		color: "from-sky-500",
		titleColor: "from-sky-500 to-sky-300",
		description: "Language Learning Tools",
		categories: ["language-learning"],
	},
	{
		id: "machine-embroidery",
		name: "Machine Embroidery",
		color: "from-pink-500",
		titleColor: "from-pink-500 to-pink-300",
		description: "Machine Embroidery Tools",
		categories: ["machine-embroidery"],
	},
	{
		id: "classroom-tools",
		name: "Educational Software",
		color: "from-green-500",
		titleColor: "from-green-500 to-green-300",
		description: "Teaching & Course Management",
		categories: ["education", "student-tools", "educator-tools"],
	},
	{
		id: "llm-tools",
		name: "LLM Tools & Libraries",
		color: "from-rose-500",
		titleColor: "from-rose-500 to-rose-300",
		description: "Large Language Model Tools",
		categories: ["llm-tools"],
	},
	{
		id: "p5js",
		name: "p5.js Tools & Libraries",
		color: "from-blue-500",
		titleColor: "from-blue-500 to-blue-300",
		description: "p5.js Development Tools",
		categories: ["p5js-tools", "p5-library"],
	},
	{
		id: "physical-computing",
		name: "Physical Computing",
		color: "from-purple-500",
		titleColor: "from-purple-500 to-purple-300",
		description: "Microcontroller & Sensor Tools",
		categories: ["physical-computing"],
	},
	{
		id: "legacy-libraries",
		name: "Legacy Libraries",
		color: "from-gray-500",
		titleColor: "from-gray-500 to-gray-300",
		// Lifecycle categories only: sharing `library` with the section above
		// would list every current library here as well.
		description: "Historical JavaScript & Ruby Libraries",
		categories: ["legacy-libraries", "historical-js", "openlaszlo"],
	},
];

export const DevToolsSections: Section[] = [
	{
		id: "version-control",
		name: "Version Control",
		color: "from-green-500",
		titleColor: "from-green-500 to-green-300",
		description: "Tools and scripts for Git and Jujutsu version control systems.",
		categories: ["version-control"],
		topics: ["version-control", "git", "jj", "jujutsu", "vcs"],
	},
	{
		id: "p5js-tools",
		name: "p5.js Tools",
		color: "from-blue-500",
		titleColor: "from-blue-500 to-blue-300",
		description: "Development tools for the p5.js creative coding framework.",
		categories: ["p5js-tools"],
	},
];

export const ResearchSections: Section[] = [
	{
		id: "research-computing",
		name: "Research Computing",
		color: "from-cyan-500",
		titleColor: "from-cyan-500 to-cyan-300",
		description: "Tools for scheduling, running, and monitoring computational research.",
		categories: ["research-computing"],
		topics: ["gpu-computing", "job-scheduling", "research-infrastructure", "job-monitoring"],
	},
	{
		id: "research-documents",
		name: "Research Documents",
		color: "from-purple-500",
		titleColor: "from-purple-500 to-purple-300",
		description: "Tools for reading, organizing, converting, and citing scholarly documents.",
		categories: ["document-processing", "document-management", "pdf-tools", "raycast"],
		topics: ["pdf-management", "document-organization", "arxiv", "citation-management"],
	},
];

export const AgentToolsSections: Section[] = [
	{
		id: "coordination",
		name: "Coordination",
		color: "from-violet-500",
		titleColor: "from-violet-500 to-violet-300",
		description: "How independent agent sessions reach each other and avoid working over one another.",
		categories: ["agent-tools"],
	},
];

export const CLISections: Section[] = [
	{
		id: "language-learning",
		name: "Language Learning",
		color: "from-sky-500",
		titleColor: "from-sky-500 to-sky-300",
		description: "Command-line tools for language learning and translation.",
		categories: ["language-learning"],
	},
	{
		id: "development-tools",
		name: "Development Tools",
		color: "from-indigo-500",
		titleColor: "from-indigo-500 to-indigo-300",
		description: "Command-line utilities for software development and build processes.",
		categories: ["development-tools"],
	},
	{
		id: "publishing",
		name: "Publishing",
		color: "from-amber-500",
		titleColor: "from-amber-500 to-amber-300",
		description: "Command-line tools for publishing and documentation.",
		categories: ["web-publishing", "document-management"],
	},
	{
		id: "llm-tools",
		name: "LLM Tools",
		color: "from-rose-500",
		titleColor: "from-rose-500 to-rose-300",
		description: "Command-line utilities for working with large language models.",
		categories: ["llm-tools"],
	},
	{
		id: "machine-embroidery",
		name: "Machine Embroidery",
		color: "from-pink-500",
		titleColor: "from-pink-500 to-pink-300",
		description: "Command-line tools for machine embroidery file conversion.",
		categories: ["machine-embroidery"],
	},
	{
		id: "p5js",
		name: "p5.js",
		color: "from-blue-500",
		titleColor: "from-blue-500 to-blue-300",
		description: "Command-line tools for p5.js development.",
		categories: ["p5js"],
	},
	{
		id: "teaching-tools",
		name: "Classroom Assignment Management",
		color: "from-green-500",
		titleColor: "from-green-500 to-green-300",
		description: "Command-line utilities for education and teaching.",
		categories: ["education", "student-tools", "educator-tools"],
	},
	// No "Other" section: ProjectList already collects unassigned projects into a
	// catch-all group, and an explicit one filtered on categories nothing carries.
];

export const ObsidianSections: Section[] = [
	{
		id: "obsidian-plugins",
		name: "Plugins & Extensions",
		color: "from-purple-500",
		titleColor: "from-purple-500 to-purple-300",
		description: "Obsidian plugins and integrations that extend functionality.",
		categories: ["obsidian-plugin", "obsidian-integration"],
	},
	{
		id: "obsidian-cli",
		name: "Command Line Tools",
		color: "from-amber-500",
		titleColor: "from-amber-500 to-amber-300",
		description: "Command-line utilities for debugging and maintaining Obsidian vaults.",
		categories: ["cli"],
	},
];

export const LibrarySections: Section[] = [
	{
		id: "language-processing",
		name: "Language & Speech Processing",
		color: "from-purple-600",
		titleColor: "from-purple-600 to-purple-400",
		description: "Libraries for natural language processing, speech synthesis, and language detection.",
		categories: ["natural-language-processing", "speech", "language-detection", "language-learning"],
	},
	{
		id: "p5js",
		name: "p5.js Libraries",
		color: "from-blue-500",
		titleColor: "from-blue-500 to-blue-300",
		description: "Libraries and extensions for the p5.js creative coding framework.",
		categories: ["p5js", "p5-library"],
	},
	{
		id: "music",
		name: "Music Libraries",
		color: "from-orange-500",
		titleColor: "from-orange-500 to-orange-300",
		description: "Libraries for music theory, chords, and scales.",
		categories: ["music-tools"],
	},
	{
		id: "llm",
		name: "LLM Libraries",
		color: "from-rose-500",
		titleColor: "from-rose-500 to-rose-300",
		description: "Libraries for working with large language models.",
		categories: ["prompt-matrix"],
	},
	{
		id: "historical-js",
		name: "Historical JavaScript",
		color: "from-yellow-500",
		titleColor: "from-yellow-500 to-yellow-300",
		description: "Historical JavaScript libraries for functional programming and fluent interfaces.",
		categories: ["historical-js"],
	},
	{
		id: "openlaszlo",
		name: "OpenLaszlo Libraries",
		color: "from-amber-500",
		titleColor: "from-amber-500 to-amber-300",
		description: "Libraries and tools for OpenLaszlo development.",
		categories: ["openlaszlo"],
	},
];
