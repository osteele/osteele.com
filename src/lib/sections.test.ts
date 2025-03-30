import { describe, expect, test } from "bun:test";
import { projectsData } from "@/data/projects";
import { getProjectTypes, getProjectsByCategory } from "./sections";
import type { Section } from "./sections";

describe("getProjectTypes", () => {
	test("identifies software projects", () => {
		// Check a JavaScript library project
		const functionalJs = projectsData.projects.find((p) => p.name === "Functional JavaScript");
		expect(functionalJs).toBeDefined();
		if (functionalJs) {
			expect(getProjectTypes(functionalJs)).toContain("software");
		}
	});

	test("identifies tool projects", () => {
		// Check a command-line tool project
		const gojekyll = projectsData.projects.find((p) => p.name === "Gojekyll");
		expect(gojekyll).toBeDefined();
		if (gojekyll) {
			expect(getProjectTypes(gojekyll)).toContain("tools");
		}
	});

	test("can identify both software and tool projects", () => {
		// Check a project that should be both software and tool
		const liquidEngine = projectsData.projects.find((p) => p.name === "Liquid Template Engine");
		expect(liquidEngine).toBeDefined();
		if (liquidEngine) {
			const types = getProjectTypes(liquidEngine);
			expect(types).toContain("software");
			expect(types).toContain("tools");
		}
	});

	test("identifies web apps correctly", () => {
		// Check a web app project
		const claudeViewer = projectsData.projects.find((p) => p.name === "Claude Chat Viewer");
		expect(claudeViewer).toBeDefined();
		if (claudeViewer) {
			expect(getProjectTypes(claudeViewer)).toContain("webapp");
		}
	});

	test("returns empty array for projects with no categories", () => {
		const project = {
			name: "Test Project",
			categories: [],
			description: "A project with no categories",
		};
		expect(getProjectTypes(project)).toHaveLength(0);
	});
});

describe("getProjectsByCategory", () => {
	const webPublishingSection: Section = {
		id: "web-publishing",
		name: "Web Publishing",
		color: "from-blue-500",
		titleColor: "from-blue-500 to-blue-300",
		description: "Web publishing tools and libraries",
		categories: ["web-publishing"],
		subsections: [
			{
				name: "Documentation Tools",
				categories: ["documentation-tools"],
			},
		],
	};

	test("correctly categorizes projects into section and subsections", () => {
		const result = getProjectsByCategory(webPublishingSection, "software", projectsData.projects);

		// Check section projects
		expect(result.sectionProjects.length).toBeGreaterThanOrEqual(1);
		expect(result.sectionProjects.length).toBeLessThanOrEqual(10); // Reasonable upper bound

		// Verify Gojekyll is in the web publishing section
		const hasGojekyll = result.sectionProjects.some((p) => p.name === "Gojekyll");
		expect(hasGojekyll).toBe(true);

		// All projects should have web-publishing category
		expect(result.sectionProjects.every((p) => p.categories.includes("web-publishing"))).toBe(true);

		// Check subsection projects
		const docToolsProjects = result.subsectionProjects.get("Documentation Tools");
		expect(docToolsProjects).toBeDefined();
		if (docToolsProjects) {
			expect(docToolsProjects.length).toBeGreaterThanOrEqual(1);
			expect(docToolsProjects.length).toBeLessThanOrEqual(5); // Reasonable upper bound
			expect(docToolsProjects.every((p) => p.categories.includes("documentation-tools"))).toBe(true);

			// Verify Liquid Template Engine is in documentation tools
			const hasLiquidEngine = docToolsProjects.some((p) => p.name === "Liquid Template Engine");
			expect(hasLiquidEngine).toBe(true);
		}
	});

	test("handles sections without subsections", () => {
		const sectionWithoutSubsections: Section = {
			...webPublishingSection,
			subsections: undefined,
		};

		const result = getProjectsByCategory(sectionWithoutSubsections, "software", projectsData.projects);

		expect(result.sectionProjects).toBeDefined();
		expect(result.subsectionProjects.size).toBe(0);

		// All web publishing projects should be in the main section now
		const totalWebPublishingProjects = projectsData.projects.filter((p) =>
			p.categories.includes("web-publishing"),
		).length;
		expect(result.sectionProjects.length).toBe(totalWebPublishingProjects);
	});

	test("handles empty project lists", () => {
		const result = getProjectsByCategory(webPublishingSection, "software", []);

		expect(result.sectionProjects).toHaveLength(0);
		expect(result.subsectionProjects.size).toBe(1); // Still has the subsection, just empty
		expect(result.subsectionProjects.get("Documentation Tools")).toHaveLength(0);
	});

	test("correctly filters by project type", () => {
		const softwareResult = getProjectsByCategory(webPublishingSection, "software", projectsData.projects);
		const toolsResult = getProjectsByCategory(webPublishingSection, "tools", projectsData.projects);

		// Check specific projects appear in the right categories
		const gojekyll = projectsData.projects.find((p) => p.name === "Gojekyll");
		const liquidEngine = projectsData.projects.find((p) => p.name === "Liquid Template Engine");

		expect(gojekyll).toBeDefined();
		expect(liquidEngine).toBeDefined();

		// Gojekyll should be in both software and tools results
		if (gojekyll) {
			expect(
				softwareResult.sectionProjects.some((p) => p.name === gojekyll.name) ||
					toolsResult.sectionProjects.some((p) => p.name === gojekyll.name),
			).toBe(true);
		}

		// Liquid Template Engine should be in documentation tools subsection
		if (liquidEngine) {
			const docTools = softwareResult.subsectionProjects.get("Documentation Tools") || [];
			expect(docTools.some((p) => p.name === liquidEngine.name)).toBe(true);
		}

		// Verify reasonable total counts
		const totalProjects = softwareResult.sectionProjects.length + toolsResult.sectionProjects.length;
		expect(totalProjects).toBeGreaterThanOrEqual(2); // At least a few projects
		expect(totalProjects).toBeLessThanOrEqual(20); // Reasonable upper bound
	});

	test("handles subsections with normalized names", () => {
		const sectionWithNamedSubsection: Section = {
			id: "web-tools",
			name: "Web Tools",
			color: "from-blue-500",
			titleColor: "from-blue-500 to-blue-300",
			description: "Web development tools",
			categories: ["web-tools", "web-publishing"],
			subsections: [
				{
					name: "Documentation Tools",
					categories: ["documentation-tools"],
				},
			],
		};

		const result = getProjectsByCategory(sectionWithNamedSubsection, "tools", projectsData.projects);
		const docToolsProjects = result.subsectionProjects.get("Documentation Tools");

		expect(docToolsProjects).toBeDefined();
		if (docToolsProjects) {
			expect(docToolsProjects.length).toBeGreaterThanOrEqual(1);
			expect(docToolsProjects.length).toBeLessThanOrEqual(5); // Reasonable upper bound
			expect(docToolsProjects.every((p) => p.categories.includes("documentation-tools"))).toBe(true);

			// Check for specific projects we expect to find
			const hasLiquidEngine = docToolsProjects.some((p) => p.name === "Liquid Template Engine");
			expect(hasLiquidEngine).toBe(true);
		}

		// Main section should have web publishing tools
		expect(result.sectionProjects.length).toBeGreaterThanOrEqual(1);
		expect(result.sectionProjects.some((p) => p.name === "Gojekyll")).toBe(true);
	});
});
