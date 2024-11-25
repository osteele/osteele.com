import { PageLayout } from "@/components/page-layout";
import {
  Section,
  getProjectsByCategory,
} from "@/lib/sections";
import { ProjectCard } from "@/components/project-card";
import { SectionNav } from "@/components/section-nav";
import { SoftwareSections } from "@/data/sections";

function SectionContent({ section }: { section: Section }) {
  const projectData = getProjectsByCategory(section, "software");

  return (
    <div className="grid gap-6">
      {section.subsections ? (
        <>
          {projectData.sectionProjects.length > 0 && (
            <div className="bg-white/50 dark:bg-gray-800/50 rounded-lg backdrop-blur-sm border border-gray-200 dark:border-gray-700">
              <div className="p-6">
                <div className="space-y-4">
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
                  <div className="space-y-4">
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
            <div className="space-y-4">
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
              </p>
              <SectionContent section={section} />
            </div>
          </section>
        ))}
      </div>
    </PageLayout>
  );
}
