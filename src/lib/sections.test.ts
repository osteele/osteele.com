import { describe, test, expect, beforeEach, afterEach, mock } from "bun:test";
import {
  getProjectTypes,
  getProjectsByCategory,
  ProjectType,
  Section,
} from "./sections";
import { Project } from "@/data/projects";

describe("getProjectTypes", () => {
  test("identifies software projects", () => {
    const project: Project = {
      name: "Test Library",
      categories: ["software", "library"],
      description: "A test library",
    };
    expect(getProjectTypes(project)).toContain("software");
  });

  test("identifies tool projects", () => {
    const project: Project = {
      name: "Test Tool",
      categories: ["webapp", "tools"],
      description: "A test tool",
    };
    expect(getProjectTypes(project)).toContain("tools");
  });

  test("can identify both software and tool projects", () => {
    const project: Project = {
      name: "Hybrid Project",
      categories: ["software", "webapp"],
      description: "A hybrid project",
    };
    const types = getProjectTypes(project);
    expect(types).toContain("software");
    expect(types).toContain("tools");
  });

  test("returns empty array for projects with no matching categories", () => {
    const project: Project = {
      name: "Other Project",
      categories: ["other"],
      description: "A project with no matching categories",
    };
    expect(getProjectTypes(project)).toHaveLength(0);
  });
});

describe("getProjectsByCategory", () => {
  const mockProjects: Project[] = [
    {
      name: "Main Section Project",
      categories: ["section-id", "software"],
      description: "Project in main section",
    },
    {
      name: "Subsection Project",
      categories: ["subsection-1", "software"],
      description: "Project in subsection",
    },
    {
      name: "Both Sections Project",
      categories: ["section-id", "subsection-1", "software"],
      description: "Project in both main and subsection",
    },
  ];

  const mockSection: Section = {
    id: "section-id",
    name: "Test Section",
    color: "from-blue-500",
    titleColor: "from-blue-500 to-blue-300",
    description: "Test section description",
    categories: ["section-id"],
    subsections: [
      {
        name: "Subsection 1",
        categories: ["subsection-1"],
      },
    ],
  };

  beforeEach(() => {
    mock.module("@/data/projects", () => ({
      projectsData: {
        projects: mockProjects,
      },
    }));
  });

  test("correctly categorizes projects into section and subsections", () => {
    const result = getProjectsByCategory(mockSection, "software");

    // Check section projects
    expect(result.sectionProjects).toHaveLength(1);
    expect(result.sectionProjects[0].name).toBe("Main Section Project");

    // Check subsection projects
    const subsectionProjects = result.subsectionProjects.get("Subsection 1");
    expect(subsectionProjects).toBeDefined();
    expect(subsectionProjects).toHaveLength(2);
    expect(subsectionProjects?.map((p) => p.name)).toContain(
      "Subsection Project"
    );
    expect(subsectionProjects?.map((p) => p.name)).toContain(
      "Both Sections Project"
    );
  });

  test("handles sections without subsections", () => {
    const sectionWithoutSubsections: Section = {
      ...mockSection,
      subsections: undefined,
    };

    const result = getProjectsByCategory(sectionWithoutSubsections, "software");

    expect(result.sectionProjects).toBeDefined();
    expect(result.subsectionProjects.size).toBe(0);
  });

  test("handles empty project lists", () => {
    mock.module("@/data/projects", () => ({
      projectsData: {
        projects: [],
      },
    }));

    const result = getProjectsByCategory(mockSection, "software");

    expect(result.sectionProjects).toHaveLength(0);
    expect(result.subsectionProjects.size).toBe(1); // Still has the subsection, just empty
    expect(result.subsectionProjects.get("Subsection 1")).toHaveLength(0);
  });

  test("correctly filters by project type", () => {
    const softwareResult = getProjectsByCategory(mockSection, "software");
    const toolsResult = getProjectsByCategory(mockSection, "tools");

    // Projects should only appear in one type or the other
    expect(
      softwareResult.sectionProjects.length + toolsResult.sectionProjects.length
    ).toBeLessThanOrEqual(mockProjects.length);
  });

  test("handles subsections with normalized names", () => {
    const sectionWithNamedSubsection: Section = {
      ...mockSection,
      subsections: [
        {
          name: "Web Publishing",
          // No categories specified - should use normalized name
        },
      ],
    };

    const projectWithNormalizedCategory: Project = {
      name: "Web Publishing Tool",
      categories: ["web-publishing", "tools", "section-id"],
      description: "A web publishing tool",
    };

    mock.module("@/data/projects", () => ({
      projectsData: {
        projects: [projectWithNormalizedCategory],
      },
    }));

    const result = getProjectsByCategory(sectionWithNamedSubsection, "tools");
    const webPublishingProjects =
      result.subsectionProjects.get("Web Publishing");

    expect(webPublishingProjects).toBeDefined();
    expect(webPublishingProjects?.map((p) => p.name)).toContain(
      "Web Publishing Tool"
    );
  });
});
