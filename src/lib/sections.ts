import { projectsData } from "@/data/projects";
import type { Project, ProjectCategory } from "@/data/projects.types";

export interface Subsection {
	name: string;
	categories?: string[];
}

export interface Section {
	id: string;
	name: string;
	color: string;
	titleColor: string;
	description: string;
	subsections?: Subsection[];
	categories?: string[];
	projectType?: "software" | "webapp" | "tools" | "educational";
}

export type ProjectType = "software" | "webapp" | "tools" | "educational";

// Categories that indicate project types
const WEB_APP_CATEGORIES = new Set(["web-app", "webapp"]);
const COMMAND_LINE_CATEGORIES = new Set([
	"command-line-tool",
	"cli",
	"tools",
	"p5js-tools",
	"web-tools",
	"documentation-tools",
	"development-tools",
	"web-publishing", // Web publishing tools are also tools
	"llm-tools", // LLM tools are also tools
	"ai-tools", // AI tools are also tools
	"student-tools", // Student tools are also tools
	"educator-tools", // Educator tools are also tools
]);
const EDUCATION_CATEGORIES = new Set(["education", "student-tools", "educator-tools"]);

export const getProjectTypes = (project: Project): ProjectType[] => {
	const projectCategories = new Set<string>(project.categories);
	const types: ProjectType[] = [];

	// For software page, include all projects
	if (projectCategories.size > 0) {
		types.push("software");
	}

	// For web apps page (formerly tools page)
	if (hasIntersection(projectCategories, WEB_APP_CATEGORIES)) {
		types.push("webapp");
	}

	// For tools distinction on software page
	if (hasIntersection(projectCategories, COMMAND_LINE_CATEGORIES)) {
		types.push("tools");
	}

	// For educational software page
	if (hasIntersection(projectCategories, EDUCATION_CATEGORIES)) {
		types.push("educational");
	}

	return types;
};

const hasIntersection = (setA: Set<string>, setB: Set<string>): boolean => {
	for (const elem of setA) {
		if (setB.has(elem)) return true;
	}
	return false;
};

export interface SectionProjects {
	sectionProjects: Project[];
	subsectionProjects: Map<string, Project[]>;
}

/**
 * Categorizes projects into a section and its subsections.
 *
 * This function takes a section and a list of projects, and returns:
 * 1. sectionProjects: Projects that belong to the section but not to any subsection
 * 2. subsectionProjects: A map of subsection name to projects that belong to that subsection
 *
 * Projects are assigned to a section if they have the section's id as a category,
 * or if they match any of the section's categories.
 *
 * Projects are assigned to a subsection if:
 * - The subsection has explicit categories and the project has at least one of them, OR
 * - The project has a category that matches the normalized subsection name (lowercase, spaces replaced with hyphens)
 *
 * A project can appear in multiple subsections if it matches the criteria for each.
 */
export const getProjectsByCategory = (section: Section, projects: Project[]): SectionProjects => {
	// Get all projects that match the section criteria
	const allSectionProjects = projects.filter((project) => {
		const projectCategories = new Set<string>(project.categories);
		const types = getProjectTypes(project);

		// Match if project type matches section type (if section specifies one)
		const typeMatch = section.projectType ? types.includes(section.projectType) : false;

		// Match if project has section's ID as a category
		const idMatch = projectCategories.has(section.id);

		// Match if project intersects with section's explicit categories (if any)
		const categoryMatch = section.categories?.length
			? hasIntersection(projectCategories, new Set<string>(section.categories))
			: false;

		// Project belongs to the section if any of these match
		return typeMatch || idMatch || categoryMatch;
	});

	// Initialize subsection map
	const subsectionProjects = new Map<string, Project[]>();

	// Initialize set to track projects that belong to subsections
	const projectsInSubsections = new Set<Project>();

	// Check if the section already has an "Other" subsection
	const hasOtherSubsection = section.subsections?.some(
		(sub) => sub.name === "Other" || normalizeSubsectionName(sub.name) === "other",
	);

	// Remove any explicit "Other" subsection - we'll add it dynamically
	const filteredSubsections = section.subsections?.filter(
		(sub) => sub.name !== "Other" && normalizeSubsectionName(sub.name) !== "other",
	);

	// Assign projects to subsections if they exist
	if (filteredSubsections?.length) {
		filteredSubsections.forEach((subsection) => {
			const subsectionMatches = allSectionProjects.filter((project) => {
				const projectCategories = new Set<string>(project.categories);

				// If subsection has explicit categories, check for intersection
				if (subsection.categories?.length) {
					return hasIntersection(projectCategories, new Set<string>(subsection.categories));
				}

				// If no explicit categories, use normalized subsection name
				const normalizedName = normalizeSubsectionName(subsection.name);
				return projectCategories.has(normalizedName);
			});

			subsectionProjects.set(normalizeSubsectionName(subsection.name), subsectionMatches);
			subsectionMatches.forEach((project) => projectsInSubsections.add(project));
		});
	}

	// Get projects that belong to section but not to any explicit subsection
	const uncategorizedProjects = allSectionProjects.filter((project) => !projectsInSubsections.has(project));

	// Decide whether to show uncategorized projects in main section or in "Other" subsection
	let sectionOnlyProjects: Project[] = [];

	// If there are subsections and uncategorized projects, add an "Other" subsection
	if (filteredSubsections?.length && uncategorizedProjects.length > 0 && !hasOtherSubsection) {
		// Add an "Other" subsection with uncategorized projects
		subsectionProjects.set("Other", uncategorizedProjects);
	} else {
		// If no subsections or no uncategorized projects, show projects in main section
		sectionOnlyProjects = uncategorizedProjects;
	}

	return {
		sectionProjects: sectionOnlyProjects,
		subsectionProjects,
	};
};

// Helper function to normalize subsection names
const normalizeSubsectionName = (name: string): string => {
	return name.toLowerCase().replace(/ /g, "-");
};
