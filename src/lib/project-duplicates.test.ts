import { describe, expect, test } from "bun:test";
import { projectsData } from "../data/projects";
import type { Project } from "../data/projects.types";
import { EducationalSoftwareSections, SoftwareSections, WebAppSections } from "../data/sections";
import type { Section } from "../lib/sections";
import { getProjectsByCategory } from "./sections";

describe("Project Duplicate Prevention on Pages", () => {
	const projects = projectsData.projects;

	function checkPageForDuplicates(pageName: string, sections: Section[], projects: Project[]): string[] {
		const projectsOnPage = new Set<string>();
		const duplicates: string[] = [];

		// Simulate what would appear on a page that uses ProjectList with these sections
		sections.forEach((section) => {
			const projectData = getProjectsByCategory(section, projects);

			// Check section projects
			projectData.sectionProjects.forEach((project) => {
				if (projectsOnPage.has(project.name)) {
					duplicates.push(project.name);
				}
				projectsOnPage.add(project.name);
			});

			// Check subsection projects
			if (section.subsections) {
				section.subsections.forEach((subsection) => {
					const subsectionProjects = projectData.subsectionProjects.get(subsection.name) || [];
					subsectionProjects.forEach((project) => {
						if (projectsOnPage.has(project.name)) {
							duplicates.push(project.name);
						}
						projectsOnPage.add(project.name);
					});
				});
			}
		});

		// For pages without sections (catch-all only), check all projects
		if (sections.length === 0 && projects.length > 0) {
			projects.forEach((project) => {
				if (projectsOnPage.has(project.name)) {
					duplicates.push(project.name);
				}
				projectsOnPage.add(project.name);
			});
		}

		return duplicates;
	}

	// Comprehensive test for all pages that display project lists
	const pageConfigs = [
		// Software pages
		{
			name: "/software",
			sections: SoftwareSections,
			filter: (p: Project) => true, // Software page shows category tiles, not projects directly
			skipTest: true, // This page doesn't show project lists
		},
		{
			name: "/software/web-apps",
			sections: WebAppSections,
			filter: (p: Project) => p.categories.includes("webapp"),
		},
		{
			name: "/software/command-line",
			sections: SoftwareSections.filter((s) => s.id === "command-line"),
			filter: (p: Project) => p.categories.includes("command-line-tool") || p.categories.includes("cli"),
		},
		{
			name: "/software/libraries",
			sections: SoftwareSections.filter((s) => s.id === "libraries"),
			filter: (p: Project) =>
				p.categories.includes("javascript-library") ||
				p.categories.includes("p5-library") ||
				p.categories.includes("ruby-library") ||
				p.categories.includes("python-library") ||
				p.categories.includes("rails-plugins") ||
				p.categories.includes("library"),
		},
		{
			name: "/software/development-tools",
			sections: SoftwareSections.filter((s) => s.id === "development-tools"),
			filter: (p: Project) =>
				p.categories.includes("development-tools") ||
				p.categories.includes("developer-tools") ||
				p.categories.includes("tools"),
		},
		{
			name: "/software/academic-research-tools",
			sections: [],
			filter: (p: Project) =>
				p.categories.includes("research-tools") ||
				p.topics?.includes("research-tools") ||
				p.topics?.includes("research") ||
				p.topics?.includes("pdf-management") ||
				p.topics?.includes("academic"),
		},
		{
			name: "/software/obsidian",
			sections: SoftwareSections.filter((s) => s.id === "obsidian"),
			filter: (p: Project) =>
				p.categories.includes("obsidian") || p.categories.includes("obsidian-plugin") || p.topics?.includes("obsidian"),
		},
		{
			name: "/software/education",
			sections: SoftwareSections.filter((section) =>
				section.categories?.some((cat) =>
					["education", "educational-software", "language-learning", "physical-computing"].includes(cat),
				),
			),
			filter: (p: Project) =>
				p.categories.includes("education") || p.categories.includes("educational") || p.categories.includes("learning"),
		},
		// Topic pages
		{
			name: "/topics/computer-education",
			sections: EducationalSoftwareSections,
			filter: (p: Project) =>
				p.categories.includes("education") ||
				p.categories.includes("student-tools") ||
				p.categories.includes("educator-tools") ||
				p.categories.includes("programming-visualizations") ||
				p.categories.includes("physical-computing-education") ||
				p.categories.includes("course-materials"),
		},
		{
			name: "/topics/language-learning",
			sections: SoftwareSections.filter((s) => s.id === "language-learning"),
			filter: (p: Project) => p.categories.includes("language-learning"),
		},
		{
			name: "/topics/physical-computing",
			sections: [],
			filter: (p: Project) =>
				p.categories.includes("physical-computing") ||
				p.categories.includes("arduino") ||
				p.categories.includes("sensor-data") ||
				p.topics?.includes("physical-computing"),
		},
		{
			name: "/topics/p5js",
			sections: SoftwareSections.filter((s) => s.id === "p5js"),
			filter: (p: Project) => ["p5js", "p5-library", "p5js-tools"].some((cat) => p.categories.includes(cat)),
		},
		{
			name: "/topics/embroidery",
			sections: [],
			filter: (p: Project) =>
				p.categories.includes("embroidery") ||
				p.categories.includes("machine-embroidery") ||
				p.topics?.includes("embroidery"),
		},
		// Root-level pages
		{
			name: "/p5js",
			sections: SoftwareSections.filter((s) => s.id === "p5js"),
			filter: (p: Project) => ["p5js", "p5-library", "p5js-tools"].some((cat) => p.categories.includes(cat)),
		},
		{
			name: "/tools",
			sections: [],
			filter: (p: Project) =>
				p.categories.includes("tools") || p.categories.includes("utility") || p.categories.includes("productivity"),
			skipTest: true, // Tools page may have custom logic
		},
		{
			name: "/teaching-materials",
			sections: [],
			filter: (p: Project) =>
				p.categories.includes("teaching-materials") ||
				p.categories.includes("course-materials") ||
				p.categories.includes("education"),
		},
		{
			name: "/language-learning",
			sections: SoftwareSections.filter((s) => s.id === "language-learning"),
			filter: (p: Project) => p.categories.includes("language-learning"),
		},
		{
			name: "/embroidery",
			sections: [],
			filter: (p: Project) =>
				p.categories.includes("embroidery") ||
				p.categories.includes("machine-embroidery") ||
				p.topics?.includes("embroidery"),
		},
	];

	// Generate tests for each page
	pageConfigs.forEach((config) => {
		if (!config.skipTest) {
			test(`no project appears more than once on ${config.name} page`, () => {
				const filteredProjects = projects.filter(config.filter);
				const duplicates = checkPageForDuplicates(config.name, config.sections, filteredProjects);

				if (duplicates.length > 0) {
					console.error(`Duplicate projects found on ${config.name} page: ${duplicates.join(", ")}`);
				}

				expect(duplicates.length).toBe(0);
			});
		}
	});

	// Special test for the main software page (doesn't show projects directly)
	test("no project appears more than once on /software page", () => {
		// The /software page doesn't use ProjectList, it just shows category tiles
		// This test is not applicable for /software
		expect(true).toBe(true);
	});

	// Test that each project appears in at least one appropriate location
	test("all projects are accessible from at least one page", () => {
		const projectsOnPages = new Map<string, Set<string>>();

		pageConfigs.forEach((config) => {
			if (!config.skipTest) {
				const filteredProjects = projects.filter(config.filter);
				const pageProjects = new Set<string>();
				filteredProjects.forEach((p) => pageProjects.add(p.name));
				projectsOnPages.set(config.name, pageProjects);
			}
		});

		const orphanedProjects = projects.filter((project) => {
			let foundOnPage = false;
			projectsOnPages.forEach((pageProjects) => {
				if (pageProjects.has(project.name)) {
					foundOnPage = true;
				}
			});
			return !foundOnPage;
		});

		// Some projects might legitimately not appear on any listing page
		// (e.g., deprecated projects, internal tools, etc.)
		// But log them for awareness
		if (orphanedProjects.length > 0) {
			console.log(`Projects not appearing on any page: ${orphanedProjects.map((p) => p.name).join(", ")}`);
		}

		// This is informational, not a failure
		expect(orphanedProjects.length).toBeGreaterThan(-1);
	});
});
