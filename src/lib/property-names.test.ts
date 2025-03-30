import { describe, expect, test } from "bun:test";
import { projectsData } from "../data/projects";
import type { Project } from "../data/projects.types";

// This test ensures project property name consistency across the codebase
describe("Project property names", () => {
	test("project property names should be consistent with templates", () => {
		// Use the already loaded project data
		const projects = projectsData.projects;

		// Make sure we have some projects to test
		expect(projects.length).toBeGreaterThan(0);

		// Check for projects with the wrong property names
		const projectsWithWrongNames = projects.filter((project: Project) => {
			return ("homepage" in project && !("website" in project)) || ("repository" in project && !("repo" in project));
		});

		if (projectsWithWrongNames.length > 0) {
			console.warn(
				"Projects with inconsistent property names:",
				projectsWithWrongNames.map((p: Project) => p.name).join(", "),
			);
		}

		// Test to ensure we're using the correct property names
		expect(projectsWithWrongNames.length).toBe(0);
	});
});
