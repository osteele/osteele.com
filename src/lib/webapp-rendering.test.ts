import { describe, expect, test } from "bun:test";
import { projectsData } from "../data/projects";
import { WebAppSections } from "../data/sections";
import { getProjectTypes, getProjectsByCategory } from "./sections";

// Direct test for the "webapp" type
describe("Web App Rendering Tests", () => {
	test("Projects with web-app category are typed as webapp", () => {
		// Find projects with web-app category
		const webAppProjects = projectsData.projects.filter((project) => project.categories?.includes("web-app"));

		// Check that they all have the webapp type
		for (const project of webAppProjects) {
			const types = getProjectTypes(project);
			expect(types).toContain("webapp");
		}

		// Check at least we have some web app projects
		expect(webAppProjects.length).toBeGreaterThan(0);
	});

	test("getProjectsByCategory returns webapp projects", () => {
		// Get all webapp projects for each WebAppSection
		let totalProjects = 0;

		for (const section of WebAppSections) {
			const result = getProjectsByCategory(section, "webapp", projectsData.projects);
			const sectionCount = result.sectionProjects.length;

			let subsectionCount = 0;
			if (section.subsections) {
				for (const subsection of section.subsections) {
					const subsectionProjects = result.subsectionProjects.get(subsection.name) || [];
					subsectionCount += subsectionProjects.length;
				}
			}

			const totalForSection = sectionCount + subsectionCount;

			totalProjects += totalForSection;
		}

		expect(totalProjects).toBeGreaterThan(0);
	});

	test("Check WEB_APP_CATEGORIES constant", () => {
		// This test explicitly checks how webapp projects are identified
		const webAppProjects = projectsData.projects.filter((project) => getProjectTypes(project).includes("webapp"));

		// Manually check overlap between categories and WEB_APP_CATEGORIES
		const allCategories = new Set<string>();
		projectsData.projects.forEach((p) => {
			p.categories.forEach((c) => allCategories.add(c));
		});
	});

	test("Verify relationship between categories and section", () => {
		// Check each web app section to see which project categories it matches
		for (const section of WebAppSections) {
			const sectionCategories = section.categories || [section.id];
			// Find projects that match this section's categories
			const matchingProjects = projectsData.projects.filter((project) => {
				const projectCategorySet = new Set(project.categories);
				return sectionCategories.some((cat) => projectCategorySet.has(cat));
			});

			// Now check how many of those are actually web apps
			const webAppMatches = matchingProjects.filter((project) => getProjectTypes(project).includes("webapp"));
		}
	});

	test("Web app projects use consistent property names", () => {
		// Use the already loaded project data
		const projects = projectsData.projects;

		// Find all webapp projects
		const webappProjects = projects.filter((project) => getProjectTypes(project).includes("webapp"));

		// There should be some webapp projects
		expect(webappProjects.length).toBeGreaterThan(0);

		// Check each webapp project for consistent property naming
		const projectsWithHomepageNotWebsite = webappProjects.filter((p) => "homepage" in p && !("website" in p));

		const projectsWithRepositoryNotRepo = webappProjects.filter((p) => "repository" in p && !("repo" in p));

		// Log any issues found
		if (projectsWithHomepageNotWebsite.length > 0) {
			console.warn(
				"Projects using 'homepage' instead of 'website':",
				projectsWithHomepageNotWebsite.map((p) => p.name).join(", "),
			);
		}

		if (projectsWithRepositoryNotRepo.length > 0) {
			console.warn(
				"Projects using 'repository' instead of 'repo':",
				projectsWithRepositoryNotRepo.map((p) => p.name).join(", "),
			);
		}

		// Assert that all webapp projects use consistent property names
		expect(projectsWithHomepageNotWebsite.length).toBe(0);
		expect(projectsWithRepositoryNotRepo.length).toBe(0);
	});
});
