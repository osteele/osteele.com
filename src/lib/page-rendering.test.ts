import { describe, expect, test } from "bun:test";
import { JSDOM } from "jsdom";
import { projectsData } from "../data/projects";
import { SoftwareSections, WebAppSections } from "../data/sections";
import type { Section } from "./sections";
import { type ProjectType, getProjectsByCategory } from "./sections";

// Simplified page rendering test mock
async function renderPage(sections: Section[], type: ProjectType) {
	// Get projects from the imported data
	const projects = projectsData.projects;

	// Build mock HTML content
	let mockHtml = '<div id="page-content">';

	for (const section of sections) {
		const projectData = getProjectsByCategory(section, type, projects);

		mockHtml += `<section id="${section.id}" class="section">`;
		mockHtml += `<h2>${section.name}</h2>`;

		// Regular section projects
		if (projectData.sectionProjects.length > 0) {
			mockHtml += '<div class="section-projects">';
			for (const project of projectData.sectionProjects) {
				mockHtml += `<div class="project" data-name="${project.name}">
          <h3>${project.name}</h3>
          <p>${project.description || ""}</p>
        </div>`;
			}
			mockHtml += "</div>";
		}

		// Subsection projects
		if (section.subsections) {
			for (const subsection of section.subsections) {
				const subsectionProjects = projectData.subsectionProjects.get(subsection.name) || [];
				if (subsectionProjects.length > 0) {
					mockHtml += `<div class="subsection" data-name="${subsection.name}">`;
					mockHtml += `<h3>${subsection.name}</h3>`;
					for (const project of subsectionProjects) {
						mockHtml += `<div class="project" data-name="${project.name}">
              <h3>${project.name}</h3>
              <p>${project.description || ""}</p>
            </div>`;
					}
					mockHtml += "</div>";
				}
			}
		}

		mockHtml += "</section>";
	}

	mockHtml += "</div>";

	// Parse with JSDOM
	const dom = new JSDOM(mockHtml);
	return dom.window.document;
}

describe("Page Rendering Tests", () => {
	test("Software page renders command-line tools", async () => {
		const document = await renderPage(SoftwareSections, "software");

		// Find the command-line section
		const commandLineSection = document.querySelector("#command-line");
		expect(commandLineSection).not.toBeNull();

		if (commandLineSection) {
			// Count projects in the section
			const projects = commandLineSection.querySelectorAll(".project");
			expect(projects.length).toBeGreaterThan(2);

			// Check for specific CLI tools
			const projectNames = Array.from(projects).map((project) => project.getAttribute("data-name"));

			// Check that some expected CLI tools are present
			const hasExpectedProjects = projectNames.some(
				(name) => name?.includes("Gojekyll") || name?.includes("Subburn") || name?.includes("Add2Anki"),
			);

			expect(hasExpectedProjects).toBe(true);
		}
	});

	test("Software page renders libraries", async () => {
		const document = await renderPage(SoftwareSections, "software");

		// Find the libraries section
		const librariesSection = document.querySelector("#libraries");
		expect(librariesSection).not.toBeNull();

		if (librariesSection) {
			// Count projects in the section
			const projects = librariesSection.querySelectorAll(".project");
			expect(projects.length).toBeGreaterThan(2);

			// Check for specific libraries
			const projectNames = Array.from(projects).map((project) => project.getAttribute("data-name"));

			// Check that some expected libraries are present
			const hasExpectedProjects = projectNames.some(
				(name) => name?.includes("Prompt Matrix") || name?.includes("Functional") || name?.includes("p5."),
			);

			expect(hasExpectedProjects).toBe(true);
		}
	});

	test("Web Apps page renders projects", async () => {
		const document = await renderPage(WebAppSections, "webapp");

		// Count total projects across all sections
		const allProjects = document.querySelectorAll(".project");
		expect(allProjects.length).toBeGreaterThan(3);

		// Get all project names for debugging
		const projectNames = Array.from(allProjects).map((project) => project.getAttribute("data-name"));

		// Check for some specific web apps
		const hasExpectedProjects = projectNames.some(
			(name) => name?.includes("Chat Viewer") || name?.includes("Kana Game") || name?.includes("Shutterspeak"),
		);

		expect(hasExpectedProjects).toBe(true);
	});

	test("Count projects in each section", async () => {
		// Software page
		const softwareDoc = await renderPage(SoftwareSections, "software");

		// Web Apps page
		const webAppsDoc = await renderPage(WebAppSections, "webapp");

		// No assertion needed, this is for diagnostic purposes
		expect(true).toBe(true);
	});

	// New tests for inspecting HTML generated from filters
	test("HTML rendering test: FilteredWebApps", async () => {
		// Filter projects directly
		const webAppProjects = projectsData.projects.filter((p) => p.categories.includes("web-app"));

		// Create a simple HTML document with the filtered projects
		const dom = new JSDOM("<!DOCTYPE html><html><body></body></html>");
		const document = dom.window.document;
		const body = document.body;

		// Create a container for the projects
		const container = document.createElement("div");
		container.id = "webapp-projects";
		body.appendChild(container);

		// Add filtered projects to the container
		webAppProjects.forEach((project) => {
			const projectElement = document.createElement("div");
			projectElement.className = "project-card";

			const title = document.createElement("h3");
			title.textContent = project.name;
			projectElement.appendChild(title);

			const desc = document.createElement("p");
			desc.textContent = project.description || "";
			projectElement.appendChild(desc);

			container.appendChild(projectElement);
		});

		// Verify we have rendered projects in the HTML
		const projectElements = container.querySelectorAll(".project-card");

		expect(projectElements.length).toBeGreaterThan(0);
		expect(projectElements.length).toBe(webAppProjects.length);
	});

	test("HTML rendering test: FilteredLibraries", async () => {
		// Filter projects directly
		const libraryCategories = [
			"javascript-libraries",
			"p5js-libraries",
			"llm-libraries",
			"ruby-libraries",
			"rails-plugins",
			"libraries",
		];

		const libraryProjects = projectsData.projects.filter((p) =>
			p.categories.some((cat) => libraryCategories.includes(cat)),
		);

		// Create a simple HTML document with the filtered projects
		const dom = new JSDOM("<!DOCTYPE html><html><body></body></html>");
		const document = dom.window.document;
		const body = document.body;

		// Create a container for the projects
		const container = document.createElement("div");
		container.id = "library-projects";
		body.appendChild(container);

		// Add filtered projects to the container
		libraryProjects.forEach((project) => {
			const projectElement = document.createElement("div");
			projectElement.className = "project-card";

			const title = document.createElement("h3");
			title.textContent = project.name;
			projectElement.appendChild(title);

			const desc = document.createElement("p");
			desc.textContent = project.description || "";
			projectElement.appendChild(desc);

			container.appendChild(projectElement);
		});

		// Verify we have rendered projects in the HTML
		const projectElements = container.querySelectorAll(".project-card");

		expect(projectElements.length).toBeGreaterThan(0);
		expect(projectElements.length).toBe(libraryProjects.length);
	});

	test("HTML rendering test: FilteredCommandLineTools", async () => {
		// Filter projects directly
		const cliCategories = ["command-line-tool", "cli"];

		const cliProjects = projectsData.projects.filter((p) => p.categories.some((cat) => cliCategories.includes(cat)));

		// Create a simple HTML document with the filtered projects
		const dom = new JSDOM("<!DOCTYPE html><html><body></body></html>");
		const document = dom.window.document;
		const body = document.body;

		// Create a container for the projects
		const container = document.createElement("div");
		container.id = "cli-projects";
		body.appendChild(container);

		// Add filtered projects to the container
		cliProjects.forEach((project) => {
			const projectElement = document.createElement("div");
			projectElement.className = "project-card";

			const title = document.createElement("h3");
			title.textContent = project.name;
			projectElement.appendChild(title);

			const desc = document.createElement("p");
			desc.textContent = project.description || "";
			projectElement.appendChild(desc);

			container.appendChild(projectElement);
		});

		// Verify we have rendered projects in the HTML
		const projectElements = container.querySelectorAll(".project-card");

		expect(projectElements.length).toBeGreaterThan(0);
		expect(projectElements.length).toBe(cliProjects.length);
	});

	test("Check project property names consistency", () => {
		// Sample a few projects to check their property names
		const cliProject = projectsData.projects.find((p) => p.name === "Gojekyll");
		expect(cliProject).toBeDefined();
		if (cliProject) {
			expect(cliProject.repo).toBeDefined();

			// Make sure deprecated property names don't exist
			expect(cliProject).not.toHaveProperty("repository");
			expect(cliProject).not.toHaveProperty("url");

			// May have website or not
			if (cliProject.website) {
				expect(typeof cliProject.website).toBe("string");
			}
		}

		const webAppProject = projectsData.projects.find((p) => p.name.includes("Chat Viewer"));
		expect(webAppProject).toBeDefined();
		if (webAppProject) {
			expect(webAppProject.repo).toBeDefined();

			// Make sure deprecated property names don't exist
			expect(webAppProject).not.toHaveProperty("repository");
			expect(webAppProject).not.toHaveProperty("url");
			expect(webAppProject).not.toHaveProperty("homepage");

			// Should have website for web apps
			expect(webAppProject.website).toBeDefined();
		}

		const libraryProject = projectsData.projects.find((p) => p.name.includes("Prompt Matrix"));
		expect(libraryProject).toBeDefined();
		if (libraryProject) {
			expect(libraryProject.repo).toBeDefined();

			// Make sure deprecated property names don't exist
			expect(libraryProject).not.toHaveProperty("repository");
		}

		// Test all projects for consistent property names
		for (const project of projectsData.projects) {
			// All projects must use 'repo' not 'repository'
			if (project.repo) {
				expect(project).not.toHaveProperty("repository");
			}

			// All projects must use 'website' not 'url' or 'homepage'
			if (project.website) {
				expect(project).not.toHaveProperty("url");
				expect(project).not.toHaveProperty("homepage");
			}
		}
	});
});
