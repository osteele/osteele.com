import { describe, expect, test } from "bun:test";
import { projectsData } from "../data/projects";
import type { Project } from "../data/projects.types";

describe("Category Detection Tests", () => {
	// Helper function to count categories across all projects
	function countCategoriesUsage() {
		const categoryCount = new Map<string, number>();

		for (const project of projectsData.projects) {
			for (const category of project.categories) {
				const count = categoryCount.get(category) || 0;
				categoryCount.set(category, count + 1);
			}
		}

		return categoryCount;
	}

	// Test that we have sufficient test data
	test("Project data should have sufficient test projects", () => {
		expect(projectsData.projects.length).toBeGreaterThan(10);
	});

	// Test web app category detection
	test("Web app category detection should work correctly", () => {
		const webAppProjects = projectsData.projects.filter((p) => p.categories.includes("web-app"));

		// Should find some web app projects
		expect(webAppProjects.length).toBeGreaterThan(0);
	});

	// Test library category detection
	test("Library category detection should work correctly", () => {
		const libraryCategories = [
			"javascript-library",
			"p5-library",
			"python-library",
			"ruby-library",
			"rails-plugins",
			"library",
		];

		const libraryProjects = projectsData.projects.filter((p) =>
			p.categories.some((cat) => libraryCategories.includes(cat)),
		);

		// Should find some library projects
		expect(libraryProjects.length).toBeGreaterThan(0);
	});

	// Test command line tool category detection
	test("Command line tool category detection should work correctly", () => {
		const cliCategories = ["command-line-tool", "cli"];

		const cliProjects = projectsData.projects.filter((p) => p.categories.some((cat) => cliCategories.includes(cat)));

		// Should find some CLI projects
		expect(cliProjects.length).toBeGreaterThan(0);

		// Log first few for inspection
		const sampleProjects = cliProjects.slice(0, 3);
		sampleProjects.forEach((_project) => {});

		// Specifically look for command-line-tool category
		const cmdLineToolProjects = projectsData.projects.filter((p) => p.categories.includes("command-line-tool"));

		// CLI category check is handled in other tests
	});

	// Show all categories used in data
	test("List all categories used in the project data", () => {
		const categories = new Set<string>();

		for (const project of projectsData.projects) {
			for (const category of project.categories) {
				categories.add(category);
			}
		}

		// Count usage of each category
		const categoryCounts = countCategoriesUsage();
		const sortedCounts = [...categoryCounts.entries()].sort((a, b) => b[1] - a[1]).slice(0, 10);
	});

	// Test finding a project by name
	test("Should be able to find specific projects by name", () => {
		// Command line tools
		const gojekyll = projectsData.projects.find((p) => p.name === "Gojekyll");
		expect(gojekyll).toBeDefined();
		if (gojekyll) {
			expect(gojekyll.categories).toContain("command-line-tool");
		}

		// Web apps
		const claudeViewer = projectsData.projects.find((p) => p.name === "Claude Chat Viewer");
		expect(claudeViewer).toBeDefined();
		if (claudeViewer) {
			expect(claudeViewer.categories).toContain("web-app");
		}

		// Libraries
		const functionalJs = projectsData.projects.find((p) => p.name === "Functional JavaScript");
		expect(functionalJs).toBeDefined();
		if (functionalJs) {
			expect(functionalJs.categories).toContain("javascript-library");
		}
	});
});
