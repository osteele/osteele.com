import { describe, expect, test } from "bun:test";
import { projectsData } from "@/data/projects";
import { EducationalSoftwareSections, SoftwareSections, WebAppSections } from "@/data/sections";
import type { Section, Subsection } from "@/lib/sections";
import { getProjectTypes, getProjectsByCategory } from "./sections";

describe("Project Categorization", () => {
	test("all projects are categorized as either tools or software", () => {
		const uncategorizedProjects = projectsData.projects.filter((project) => {
			const types = getProjectTypes(project);
			return types.length === 0;
		});

		if (uncategorizedProjects.length > 0) {
		}

		expect(uncategorizedProjects).toHaveLength(0);
	});

	test("web apps page shows projects", () => {
		let totalWebAppProjects = 0;

		// Check each section
		WebAppSections.forEach((section: Section) => {
			const projectData = getProjectsByCategory(section, "webapp", projectsData.projects);
			const sectionProjects = projectData.sectionProjects.length;

			// Add subsection projects
			let subsectionProjectsCount = 0;
			if (section.subsections) {
				section.subsections.forEach((subsection: Subsection) => {
					const subsectionProjects = projectData.subsectionProjects.get(subsection.name) || [];
					subsectionProjectsCount += subsectionProjects.length;
				});
			}

			totalWebAppProjects += sectionProjects + subsectionProjectsCount;
		});

		// Expect at least a few web apps
		expect(totalWebAppProjects).toBeGreaterThan(3);

		// Spot check specific web apps
		const webAppProjects = projectsData.projects.filter((p) => p.categories?.includes("web-app"));

		// Check that some specific web app is present
		expect(
			webAppProjects.some(
				(p) => p.name.includes("Travel Photo") || p.name.includes("Kana") || p.name.includes("Chat Viewer"),
			),
		).toBe(true);
	});

	test("command line tools section shows projects", () => {
		// Get the command line section from SoftwareSections
		const commandLineSection = SoftwareSections.find((section: Section) => section.id === "command-line");
		expect(commandLineSection).toBeDefined();

		if (commandLineSection) {
			const projectData = getProjectsByCategory(commandLineSection, "software", projectsData.projects);
			const totalProjects = projectData.sectionProjects.length;

			// Print out what we found
			// Count all command line tool projects
			const allCliProjects = projectsData.projects.filter((p) => p.categories?.includes("command-line-tool"));

			// Expect at least a few command line tools
			expect(totalProjects).toBeGreaterThan(2);

			// Check that some specific CLI tool is present
			expect(
				allCliProjects.some(
					(p) => p.name.includes("Subburn") || p.name.includes("Add2Anki") || p.name.includes("Stitch Sync"),
				),
			).toBe(true);
		}
	});

	test("libraries section shows projects", () => {
		// Get the libraries section from SoftwareSections
		const librariesSection = SoftwareSections.find((section: Section) => section.id === "libraries");
		expect(librariesSection).toBeDefined();

		if (librariesSection) {
			const projectData = getProjectsByCategory(librariesSection, "software", projectsData.projects);
			const totalProjects = projectData.sectionProjects.length;

			// Expect at least a few libraries
			expect(totalProjects).toBeGreaterThan(2);

			// Spot check specific libraries
			const libraryProjects = projectsData.projects.filter(
				(p) =>
					p.categories &&
					(p.categories.includes("javascript-libraries") ||
						p.categories.includes("llm-libraries") ||
						p.categories.includes("p5js-libraries")),
			);

			// Check that some specific library is present
			expect(
				libraryProjects.some(
					(p) =>
						p.name.includes("Prompt Matrix") ||
						p.name.includes("Functional") ||
						p.name.includes("p5.") ||
						p.name.includes("layers"),
				),
			).toBe(true);
		}
	});

	test("physical computing and embroidery pages show projects", () => {
		// Check physical computing projects
		const physicalComputingProjects = projectsData.projects.filter((p) => p.categories?.includes("physical-computing"));
		expect(physicalComputingProjects.length).toBeGreaterThan(1);

		// Check machine embroidery projects
		const embroideryProjects = projectsData.projects.filter((p) => p.categories?.includes("machine-embroidery"));
		expect(embroideryProjects.length).toBeGreaterThan(0);
	});

	// Helper function to print all categories in use
	test("list all categories in use", () => {
		const categories = new Set(projectsData.projects.flatMap((project) => project.categories));
		expect(true).toBe(true); // Dummy assertion
	});

	// Test specific projects are properly categorized
	test("key projects are assigned to the right categories", () => {
		// Command-line tools
		const gojekyll = projectsData.projects.find((p) => p.name === "Gojekyll");
		expect(gojekyll).toBeDefined();
		if (gojekyll) {
			expect(gojekyll.categories).toContain("command-line-tool");
		}

		// Libraries
		const promptMatrix = projectsData.projects.find((p) => p.name === "Prompt Matrix (JS)");
		expect(promptMatrix).toBeDefined();
		if (promptMatrix) {
			expect(promptMatrix.categories).toContain("llm-libraries");
		}

		// Web apps
		const claudeViewer = projectsData.projects.find((p) => p.name === "Claude Chat Viewer");
		expect(claudeViewer).toBeDefined();
		if (claudeViewer) {
			expect(claudeViewer.categories).toContain("web-app");
		}
	});

	// Helper function to print all section categories
	test("list all section categories", () => {
		const webAppCategories = new Set(
			WebAppSections.flatMap((section: Section) => [
				section.id,
				...(section.categories || []),
				...(section.subsections?.flatMap((sub: Subsection) => sub.categories || []) || []),
			]),
		);
		const softwareCategories = new Set(
			SoftwareSections.flatMap((section: Section) => [
				section.id,
				...(section.categories || []),
				...(section.subsections?.flatMap((sub: Subsection) => sub.categories || []) || []),
			]),
		);
		expect(true).toBe(true); // Dummy assertion
	});
});
