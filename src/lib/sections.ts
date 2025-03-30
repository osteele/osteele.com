import { projectsData } from "@/data/projects";
import type { Project } from "@/data/projects.types";

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
}

export type ProjectType = "software" | "webapp" | "tools" | "educational";

// Categories that indicate project types
const WEB_APP_CATEGORIES = new Set(["web-app"]);
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

export const getProjectsByCategory = (
	section: Section,
	type: ProjectType,
	projects = projectsData.projects,
): SectionProjects => {
	// Get projects for this section based on type

	// Get all projects that match the section criteria
	const allSectionProjects = projects.filter((project) => {
		const projectCategories = new Set<string>(project.categories);

		// If project matches any of the section's categories, include it
		if (section.categories?.length) {
			// For other types or if no specific categories, use regular matching
			const hasMatch = hasIntersection(projectCategories, new Set<string>(section.categories));
			// Check if categories match
			return hasMatch;
		}

		// If no explicit categories, only match projects that have the section.id
		return projectCategories.has(section.id);
	});

	// Initialize subsection map
	const subsectionProjects = new Map<string, Project[]>();

	// Initialize set to track projects that belong to subsections
	const projectsInSubsections = new Set<Project>();

	// Assign projects to subsections if they exist
	if (section.subsections) {
		section.subsections.forEach((subsection) => {
			const subsectionMatches = allSectionProjects.filter((project) => {
				const projectCategories = new Set<string>(project.categories);

				if (subsection.categories?.length) {
					// Project must match at least one subsection category
					return hasIntersection(projectCategories, new Set<string>(subsection.categories));
				}
				// If no explicit categories, use normalized subsection name
				const normalizedName = subsection.name.toLowerCase().replace(/ /g, "-");
				return projectCategories.has(normalizedName);
			});

			subsectionProjects.set(subsection.name, subsectionMatches);
			subsectionMatches.forEach((project) => projectsInSubsections.add(project));
		});
	}

	// Get projects that belong to section but not to any subsection
	const sectionOnlyProjects = allSectionProjects.filter((project) => !projectsInSubsections.has(project));

	return {
		sectionProjects: sectionOnlyProjects,
		subsectionProjects,
	};
};
