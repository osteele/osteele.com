import { PageLayout } from "@/components/page-layout";
import { ProjectCard } from "@/components/project-card";
import { SectionNav } from "@/components/section-nav";
import { SoftwareSections } from "@/data/sections";
import {
  Section,
  getProjectsByCategory,
} from "@/lib/sections";
import Link from "next/link";

function SectionContent({ section }: { section: Section }) {
  const projectData = getProjectsByCategory(section, "software");

  return (
    <div className="grid gap-6">
      {section.subsections ? (
        <>
          {projectData.sectionProjects.length > 0 && (
            <div className="bg-white/50 dark:bg-gray-800/50 rounded-lg backdrop-blur-sm border border-gray-200 dark:border-gray-700">
              <div className="p-6">
                <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                  {projectData.sectionProjects.map((project) => (
                    <ProjectCard key={project.name} project={project} />
                  ))}
                </div>
              </div>
            </div>
          )}
          {section.subsections.map((subsection) => {
            const subsectionProjects =
              projectData.subsectionProjects.get(subsection.name) || [];
            if (subsectionProjects.length === 0) return null;

            return (
              <div
                key={subsection.name}
                className="bg-white/50 dark:bg-gray-800/50 rounded-lg backdrop-blur-sm border border-gray-200 dark:border-gray-700"
              >
                <div className="p-6">
                  <h3
                    className={`text-xl font-semibold mb-4 text-${section.color.replace(
                      "from-",
                      ""
                    )}-700 dark:text-${section.color.replace("from-", "")}-300`}
                  >
                    {subsection.name}
                  </h3>
                  <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    {subsectionProjects.map((project) => (
                      <ProjectCard key={project.name} project={project} />
                    ))}
                  </div>
                </div>
              </div>
            );
          })}
        </>
      ) : (
        <div className="bg-white/50 dark:bg-gray-800/50 rounded-lg backdrop-blur-sm border border-gray-200 dark:border-gray-700">
          <div className="p-6">
            <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
              {projectData.sectionProjects.map((project) => (
                <ProjectCard key={project.name} project={project} />
              ))}
            </div>
          </div>
        </div>
      )}
    </div>
  );
}

export default function SoftwarePage() {
  return (
    <PageLayout title="Software">
      <div className="max-w-5xl mx-auto px-4 mb-8">
        <div className="bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-700 rounded-lg p-4">
          <p className="text-yellow-800 dark:text-yellow-200">
            More projects are at{" "}
            <a
              href="https://github.com/osteele"
              className="text-yellow-700 dark:text-yellow-300 hover:underline"
            >
              github.com/osteele
            </a>
            , and{" "}
            <a
              href="https://observablehq.com/@osteele"
              className="text-yellow-700 dark:text-yellow-300 hover:underline"
            >
              observablehq.com/@osteele
            </a>
            .
          </p>
        </div>
      </div>

      <SectionNav
        sections={SoftwareSections}
        defaultSection="software-development"
      />

      <div className="max-w-5xl mx-auto px-4">
        {SoftwareSections.map((section) => (
          <section
            key={section.id}
            id={section.id}
            className="mb-16 scroll-mt-20"
          >
            <div
              className={`relative rounded-lg bg-gradient-to-r ${section.color}/10 to-transparent p-6`}
            >
              <h2
                className={`text-3xl font-bold mb-2 bg-gradient-to-r ${section.titleColor} bg-clip-text text-transparent`}
              >
                {section.name}
              </h2>
              <p className="text-gray-600 dark:text-gray-400 mb-6">
                {section.description}
                {section.id === "language-learning" && (
                  <span className="ml-2">
                    <Link
                      href="/language-learning"
                      className="text-sky-600 dark:text-sky-400 hover:underline"
                    >
                      View all language tools →
                    </Link>
                  </span>
                )}
              </p>
              <SectionContent section={section} />
            </div>
          </section>
        ))}
      </div>
    </PageLayout>
  );
}
