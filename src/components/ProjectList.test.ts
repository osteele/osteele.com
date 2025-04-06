import { describe, expect, test } from "bun:test";
import type { Project } from "@/data/projects.types";
import type { Section } from "@/lib/sections";
import { getProjectsByCategory } from "@/lib/sections";

describe("ProjectList Component Logic", () => {
	test("projects are correctly assigned to their specific sections", () => {
		// Create test sections with subsections
		const testSections: Section[] = [
			{
				id: "web-publishing",
				name: "Web Publishing",
				color: "from-blue-500",
				titleColor: "from-blue-500 to-blue-300",
				description: "Web publishing tools",
				subsections: [{ name: "Static Site Generators" }, { name: "Documentation Tools" }],
			},
			{
				id: "language-learning",
				name: "Language Learning",
				color: "from-green-500",
				titleColor: "from-green-500 to-green-300",
				description: "Language learning tools",
				subsections: [{ name: "Vocabulary Tools" }, { name: "Grammar Tools" }],
			},
		];

		// Create test projects
		const testProjects: Project[] = [
			{
				name: "Jekyll Site",
				description: "A static site generator",
				categories: ["web-publishing", "static-site-generators", "webapp"],
				website: "https://example.com/jekyll",
			},
			{
				name: "API Docs",
				description: "Documentation tool",
				categories: ["web-publishing", "documentation-tools", "webapp"],
				website: "https://example.com/apidocs",
			},
			{
				name: "Vocab Trainer",
				description: "Vocabulary training app",
				categories: ["language-learning", "vocabulary-tools", "webapp"],
				website: "https://example.com/vocab",
			},
			{
				name: "Grammar Checker",
				description: "Grammar checking tool",
				categories: ["language-learning", "grammar-tools", "webapp"],
				website: "https://example.com/grammar",
			},
		];

		// Test each section to ensure projects are correctly assigned
		testSections.forEach((section) => {
			const projectData = getProjectsByCategory(section, testProjects);

			// Check that projects are correctly assigned to their sections
			if (section.id === "web-publishing") {
				// Verify subsection assignments for web-publishing using normalized names
				const staticSiteProjects = projectData.subsectionProjects.get("static-site-generators") || [];
				expect(staticSiteProjects.length).toBe(1);
				expect(staticSiteProjects[0].name).toBe("Jekyll Site");

				const docToolsProjects = projectData.subsectionProjects.get("documentation-tools") || [];
				expect(docToolsProjects.length).toBe(1);
				expect(docToolsProjects[0].name).toBe("API Docs");

				// Make sure language learning projects are NOT in web publishing subsections
				expect(staticSiteProjects.some((p) => p.name === "Vocab Trainer")).toBe(false);
				expect(staticSiteProjects.some((p) => p.name === "Grammar Checker")).toBe(false);
				expect(docToolsProjects.some((p) => p.name === "Vocab Trainer")).toBe(false);
				expect(docToolsProjects.some((p) => p.name === "Grammar Checker")).toBe(false);
			} else if (section.id === "language-learning") {
				// Verify subsection assignments for language-learning using normalized names
				const vocabToolsProjects = projectData.subsectionProjects.get("vocabulary-tools") || [];
				expect(vocabToolsProjects.length).toBe(1);
				expect(vocabToolsProjects[0].name).toBe("Vocab Trainer");

				const grammarToolsProjects = projectData.subsectionProjects.get("grammar-tools") || [];
				expect(grammarToolsProjects.length).toBe(1);
				expect(grammarToolsProjects[0].name).toBe("Grammar Checker");

				// Make sure web publishing projects are NOT in language learning subsections
				expect(vocabToolsProjects.some((p) => p.name === "Jekyll Site")).toBe(false);
				expect(vocabToolsProjects.some((p) => p.name === "API Docs")).toBe(false);
				expect(grammarToolsProjects.some((p) => p.name === "Jekyll Site")).toBe(false);
				expect(grammarToolsProjects.some((p) => p.name === "API Docs")).toBe(false);
			}
		});
	});
});
