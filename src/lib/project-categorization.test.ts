import { describe, test, expect } from "bun:test";
import { projectsData, Project } from "@/data/projects";
import { getProjectTypes } from "./sections";
import { Sections as ToolsSections } from "@/app/(routes)/tools/page";
import { Sections as SoftwareSections } from "@/app/(routes)/software/page";

describe("Project Categorization", () => {
  test("all projects are categorized as either tools or software", () => {
    const uncategorizedProjects = projectsData.projects.filter((project) => {
      const types = getProjectTypes(project);
      return types.length === 0;
    });

    if (uncategorizedProjects.length > 0) {
      console.log(
        "Uncategorized projects:",
        uncategorizedProjects.map((p) => ({
          name: p.name,
          categories: p.categories,
        }))
      );
    }

    expect(uncategorizedProjects).toHaveLength(0);
  });

  test("all tools are categorized in the tools page sections", () => {
    const toolProjects = projectsData.projects.filter((project) =>
      getProjectTypes(project).includes("tools")
    );

    const toolsSectionCategories = new Set(
      ToolsSections.flatMap((section) => [
        section.id,
        ...(section.categories || []),
        ...(section.subsections?.flatMap((sub) => sub.categories || []) || []),
      ])
    );

    const uncategorizedTools = toolProjects.filter((project) => {
      // A project is considered categorized if any of its categories
      // matches any section's id or categories
      return !project.categories.some((category) =>
        toolsSectionCategories.has(category)
      );
    });

    if (uncategorizedTools.length > 0) {
      console.log(
        "Tools not matching any section:",
        uncategorizedTools.map((p) => ({
          name: p.name,
          categories: p.categories,
        }))
      );
    }

    expect(uncategorizedTools).toHaveLength(0);
  });

  test("all software is categorized in the software page sections", () => {
    const softwareProjects = projectsData.projects.filter((project) =>
      getProjectTypes(project).includes("software")
    );

    const softwareSectionCategories = new Set(
      SoftwareSections.flatMap((section) => [
        section.id,
        ...(section.categories || []),
        ...(section.subsections?.flatMap((sub) => sub.categories || []) || []),
      ])
    );

    const uncategorizedSoftware = softwareProjects.filter((project) => {
      return !project.categories.some((category) =>
        softwareSectionCategories.has(category)
      );
    });

    if (uncategorizedSoftware.length > 0) {
      console.log(
        "Software not matching any section:",
        uncategorizedSoftware.map((p) => ({
          name: p.name,
          categories: p.categories,
        }))
      );
    }

    expect(uncategorizedSoftware).toHaveLength(0);
  });

  // Helper function to print all categories in use
  test.skip("list all categories in use", () => {
    const categories = new Set(
      projectsData.projects.flatMap((project) => project.categories)
    );
    console.log("All categories in use:", [...categories].sort());
    expect(true).toBe(true); // Dummy assertion
  });

  // Helper function to print all section categories
  test.skip("list all section categories", () => {
    const toolsCategories = new Set(
      ToolsSections.flatMap((section) => [
        section.id,
        ...(section.categories || []),
        ...(section.subsections?.flatMap((sub) => sub.categories || []) || []),
      ])
    );
    const softwareCategories = new Set(
      SoftwareSections.flatMap((section) => [
        section.id,
        ...(section.categories || []),
        ...(section.subsections?.flatMap((sub) => sub.categories || []) || []),
      ])
    );
    console.log("Tools section categories:", [...toolsCategories].sort());
    console.log("Software section categories:", [...softwareCategories].sort());
    expect(true).toBe(true); // Dummy assertion
  });
});
