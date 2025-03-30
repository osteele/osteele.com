import { PageLayout } from "@/components/page-layout";
import { ProjectCard } from "@/components/project-card";
import { SoftwareSections, ToolsSections } from "@/data/sections";
import { Project } from "@/data/projects";
import { getProjectsByCategory } from "@/lib/sections";

// Get all language learning projects from both Software and Tools sections
function getLanguageLearningProjects() {
  const allProjects: Project[] = [];

  // Get projects from Software sections
  SoftwareSections.forEach((section) => {
    if (
      section.id === "language-learning" ||
      section.categories?.includes("language-learning")
    ) {
      const projectData = getProjectsByCategory(section, "software");
      allProjects.push(...projectData.sectionProjects);
      if (section.subsections) {
        section.subsections.forEach((subsection) => {
          const subsectionProjects =
            projectData.subsectionProjects.get(subsection.name) || [];
          allProjects.push(...subsectionProjects);
        });
      }
    }
  });

  // Get projects from Tools sections
  ToolsSections.forEach((section) => {
    if (
      section.id === "language-learning" ||
      section.categories?.includes("language-learning")
    ) {
      const projectData = getProjectsByCategory(section, "tools");
      allProjects.push(...projectData.sectionProjects);
      if (section.subsections) {
        section.subsections.forEach((subsection) => {
          const subsectionProjects =
            projectData.subsectionProjects.get(subsection.name) || [];
          allProjects.push(...subsectionProjects);
        });
      }
    }
  });

  // Remove duplicates (if any)
  const uniqueProjects = Array.from(
    new Map(allProjects.map((project) => [project.name, project])).values()
  );

  // Categorize projects
  const webApps = uniqueProjects.filter((project) =>
    project.categories.includes("web-app")
  );
  const cliTools = uniqueProjects.filter((project) =>
    project.categories.includes("command-line-tool")
  );
  const libraries = uniqueProjects.filter((project) =>
    project.categories.includes("libraries")
  );
  const other = uniqueProjects.filter(
    (project) =>
      !project.categories.includes("web-app") &&
      !project.categories.includes("command-line-tool") &&
      !project.categories.includes("libraries")
  );

  return { webApps, cliTools, libraries, other };
}

function ProjectSection({
  title,
  projects,
}: {
  title: string;
  projects: Project[];
}) {
  if (!projects.length) return null;

  return (
    <div className="mb-12 last:mb-0">
      <h2 className="text-2xl font-semibold mb-6 text-sky-600 dark:text-sky-400">
        {title}
      </h2>
      <div className="bg-white/50 dark:bg-gray-800/50 rounded-lg backdrop-blur-sm border border-gray-200 dark:border-gray-700">
        <div className="p-6">
          <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            {projects.map((project) => (
              <ProjectCard key={project.name} project={project} />
            ))}
          </div>
        </div>
      </div>
    </div>
  );
}

export default function LanguageLearningPage() {
  const { webApps, cliTools, libraries, other } = getLanguageLearningProjects();

  return (
    <PageLayout title="Language Learning Tools">
      <div className="max-w-5xl mx-auto px-6 py-12">
        {/* Hero section */}
        <div className="mb-16 text-center">
          <h1 className="font-serif text-6xl md:text-7xl font-bold mb-6 tracking-tight text-gray-900 dark:text-gray-100">
            Language Learning
          </h1>
          <p className="text-xl text-sky-600 dark:text-sky-400 max-w-2xl mx-auto">
            Tools and applications to assist in learning foreign languages
          </p>
        </div>

        <div className="max-w-5xl mx-auto space-y-12">
          <ProjectSection title="Web Applications" projects={webApps} />
          <ProjectSection title="Command Line Tools" projects={cliTools} />
          <ProjectSection title="Libraries" projects={libraries} />
          {other.length > 0 && (
            <ProjectSection title="Other Projects" projects={other} />
          )}

          <div className="bg-sky-50 dark:bg-sky-900/20 border border-sky-200 dark:border-sky-700 rounded-lg p-6 mt-8">
            <h3 className="text-xl font-semibold mb-4 text-sky-700 dark:text-sky-300">
              About These Projects
            </h3>
            <p className="text-sky-800 dark:text-sky-200 mb-4">
              These tools were created to support my own language learning
              journey with Mandarin Chinese.
            </p>
            <p className="text-sky-800 dark:text-sky-200">
              All projects are open source and available on{" "}
              <a
                href="https://github.com/osteele"
                className="text-sky-700 dark:text-sky-300 hover:underline"
              >
                GitHub
              </a>
              . Feel free to contribute or adapt them for your own use.
            </p>
          </div>
        </div>
      </div>
    </PageLayout>
  );
}
