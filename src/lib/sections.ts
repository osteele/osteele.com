import { Project, projectsData } from "@/data/projects";

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

export type ProjectType = "software" | "tools";

const SOFTWARE_CATEGORIES = new Set(["software", "library", "package"]);
const TOOLS_CATEGORIES = new Set(["webapp", "tools"]);

export const getProjectTypes = (project: Project): ProjectType[] => {
  const projectCategories = new Set(project.categories);
  const types: ProjectType[] = [];

  // Check for intersection between project categories and type categories
  if (hasIntersection(projectCategories, SOFTWARE_CATEGORIES)) {
    types.push("software");
  }
  if (hasIntersection(projectCategories, TOOLS_CATEGORIES)) {
    types.push("tools");
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
  type: ProjectType
): SectionProjects => {
  // Get all projects that match the section criteria
  const allSectionProjects = projectsData.projects.filter((project) => {
    const projectCategories = new Set(project.categories);

    // Project must match the page type (software or tools)
    if (!getProjectTypes(project).includes(type)) return false;

    // If project matches the section's ID or categories, include it
    if (projectCategories.has(section.id)) return true;
    if (section.categories?.length) {
      return hasIntersection(projectCategories, new Set(section.categories));
    }

    // If no explicit categories, only match projects that have the section.id
    return false;
  });

  // Initialize subsection map
  const subsectionProjects = new Map<string, Project[]>();

  // Initialize set to track projects that belong to subsections
  const projectsInSubsections = new Set<Project>();

  // Assign projects to subsections if they exist
  if (section.subsections) {
    section.subsections.forEach((subsection) => {
      // For subsections, consider ALL projects that match the type, not just section projects
      const subsectionMatches = projectsData.projects.filter((project) => {
        // Must match the project type
        if (!getProjectTypes(project).includes(type)) return false;

        const projectCategories = new Set(project.categories);

        if (subsection.categories?.length) {
          // Project must match at least one subsection category
          return hasIntersection(
            projectCategories,
            new Set(subsection.categories)
          );
        } else {
          // If no explicit categories, use normalized subsection name
          const normalizedName = subsection.name
            .toLowerCase()
            .replace(/ /g, "-");
          return projectCategories.has(normalizedName);
        }
      });

      subsectionProjects.set(subsection.name, subsectionMatches);
      subsectionMatches.forEach((project) =>
        projectsInSubsections.add(project)
      );
    });
  }

  // Get projects that belong to section but not to any subsection
  const sectionOnlyProjects = allSectionProjects.filter(
    (project) => !projectsInSubsections.has(project)
  );

  return {
    sectionProjects: sectionOnlyProjects,
    subsectionProjects,
  };
};
