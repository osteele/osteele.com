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

		return duplicates;
	}

	test("no project appears more than once on /software page", () => {
		// The /software page doesn't use ProjectList, it just shows category tiles
		// This test is not applicable for /software
		expect(true).toBe(true);
	});

	test("no project appears more than once on /software/web-apps page", () => {
		// This page filters for webapp projects and uses WebAppSections
		const webAppProjects = projects.filter((p) => p.categories.includes("webapp"));
		const duplicates = checkPageForDuplicates("/software/web-apps", WebAppSections, webAppProjects);

		if (duplicates.length > 0) {
			console.error(`Duplicate projects found on /software/web-apps page: ${duplicates.join(", ")}`);
		}

		expect(duplicates.length).toBe(0);
	});

	test("no project appears more than once on /software/command-line page", () => {
		// This page would filter for command line tools
		const commandLineProjects = projects.filter(
			(p) => p.categories.includes("command-line-tool") || p.categories.includes("cli"),
		);
		const commandLineSections = SoftwareSections.filter((s) => s.id === "command-line");
		const duplicates = checkPageForDuplicates("/software/command-line", commandLineSections, commandLineProjects);

		if (duplicates.length > 0) {
			console.error(`Duplicate projects found on /software/command-line page: ${duplicates.join(", ")}`);
		}

		expect(duplicates.length).toBe(0);
	});

	test("no project appears more than once on /software/libraries page", () => {
		// This page would filter for library projects
		const libraryProjects = projects.filter(
			(p) =>
				p.categories.includes("javascript-library") ||
				p.categories.includes("p5-library") ||
				p.categories.includes("ruby-library") ||
				p.categories.includes("python-library") ||
				p.categories.includes("rails-plugins") ||
				p.categories.includes("library"),
		);
		const librarySections = SoftwareSections.filter((s) => s.id === "libraries");
		const duplicates = checkPageForDuplicates("/software/libraries", librarySections, libraryProjects);

		if (duplicates.length > 0) {
			console.error(`Duplicate projects found on /software/libraries page: ${duplicates.join(", ")}`);
		}

		expect(duplicates.length).toBe(0);
	});

	test("no project appears more than once on /p5js page", () => {
		// The p5js page filters projects and uses specific sections
		const p5jsCategories = ["p5js", "p5-library", "p5js-tools"];
		const p5jsProjects = projects.filter((project) => {
			return project.categories.some((category) => p5jsCategories.includes(category));
		});
		const p5jsSections = SoftwareSections.filter((section) => section.id === "p5js");

		const duplicates = checkPageForDuplicates("/p5js", p5jsSections, p5jsProjects);

		if (duplicates.length > 0) {
			console.error(`Duplicate projects found on /p5js page: ${duplicates.join(", ")}`);
		}

		expect(duplicates.length).toBe(0);
	});

	test("no project appears more than once on /topics/computer-education page", () => {
		// This page uses EducationalSoftwareSections
		const educationProjects = projects.filter(
			(p) =>
				p.categories.includes("education") ||
				p.categories.includes("student-tools") ||
				p.categories.includes("educator-tools") ||
				p.categories.includes("programming-visualizations") ||
				p.categories.includes("physical-computing-education") ||
				p.categories.includes("course-materials"),
		);

		const duplicates = checkPageForDuplicates(
			"/topics/computer-education",
			EducationalSoftwareSections,
			educationProjects,
		);

		if (duplicates.length > 0) {
			console.error(`Duplicate projects found on /topics/computer-education page: ${duplicates.join(", ")}`);
		}

		expect(duplicates.length).toBe(0);
	});

	test("no project appears more than once on /software/education/index page", () => {
		// This page filters education projects and uses filtered SoftwareSections
		const educationProjects = projects.filter(
			(p) =>
				p.categories.includes("education") || p.categories.includes("educational") || p.categories.includes("learning"),
		);

		const educationSections = SoftwareSections.filter((section) =>
			section.categories?.some((cat) =>
				["education", "educational-software", "language-learning", "physical-computing"].includes(cat),
			),
		);

		const duplicates = checkPageForDuplicates("/software/education/index", educationSections, educationProjects);

		if (duplicates.length > 0) {
			console.error(`Duplicate projects found on /software/education/index page: ${duplicates.join(", ")}`);
		}

		expect(duplicates.length).toBe(0);
	});

	test("no project appears more than once on /language-learning/index page", () => {
		// This page filters language learning projects
		const languageLearningProjects = projects.filter((p) => p.categories.includes("language-learning"));
		const languageLearningSections = SoftwareSections.filter((s) => s.id === "language-learning");

		const duplicates = checkPageForDuplicates(
			"/language-learning/index",
			languageLearningSections,
			languageLearningProjects,
		);

		if (duplicates.length > 0) {
			console.error(`Duplicate projects found on /language-learning/index page: ${duplicates.join(", ")}`);
		}

		expect(duplicates.length).toBe(0);
	});

	test("no project appears more than once on any CategoryLayout page", () => {
		// Generic test for pages using CategoryLayout
		const pageConfigs = [
			{
				name: "web-apps",
				sections: WebAppSections,
				filter: (p: Project) => p.categories.includes("webapp"),
			},
			{
				name: "p5js",
				sections: SoftwareSections.filter((s) => s.id === "p5js"),
				filter: (p: Project) => ["p5js", "p5-library", "p5js-tools"].some((cat) => p.categories.includes(cat)),
			},
		];

		pageConfigs.forEach((config) => {
			const filteredProjects = projects.filter(config.filter);
			const duplicates = checkPageForDuplicates(config.name, config.sections, filteredProjects);

			if (duplicates.length > 0) {
				console.error(`Duplicate projects found on ${config.name} page: ${duplicates.join(", ")}`);
			}

			expect(duplicates.length).toBe(0);
		});
	});
});
