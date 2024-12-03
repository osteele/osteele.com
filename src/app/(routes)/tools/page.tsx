import { PageLayout } from "@/components/page-layout";
import { Section, getProjectsByCategory } from "@/lib/sections";
import { ProjectCard } from "@/components/project-card";
import { SectionNav } from "@/components/section-nav";
import { ToolsSections } from "@/data/sections";

function SectionContent({ section }: { section: Section }) {
  const toolsData = getProjectsByCategory(section, "tools");

  return (
    <div className="grid gap-8">
      {section.subsections ? (
        <>
          {toolsData.sectionProjects.length > 0 && (
            <div className="bg-white/70 dark:bg-gray-800/70 rounded-lg backdrop-blur-sm border border-gray-200 dark:border-gray-700 shadow-sm">
              <div className="p-8">
                <div className="space-y-6">
                  {toolsData.sectionProjects.map((tool) => (
                    <ProjectCard key={tool.name} project={tool} />
                  ))}
                </div>
              </div>
            </div>
          )}
          {section.subsections.map((subsection) => {
            const subsectionTools =
              toolsData.subsectionProjects.get(subsection.name) || [];
            if (subsectionTools.length === 0) return null;

            return (
              <div
                key={subsection.name}
                className="bg-white/70 dark:bg-gray-800/70 rounded-lg backdrop-blur-sm border border-gray-200 dark:border-gray-700 shadow-sm"
              >
                <div className="p-8">
                  <h3 className="text-2xl font-semibold mb-6 text-gray-800 dark:text-gray-100">
                    {subsection.name}
                  </h3>
                  <div className="space-y-6">
                    {subsectionTools.map((tool) => (
                      <ProjectCard key={tool.name} project={tool} />
                    ))}
                  </div>
                </div>
              </div>
            );
          })}
        </>
      ) : (
        <div className="bg-white/70 dark:bg-gray-800/70 rounded-lg backdrop-blur-sm border border-gray-200 dark:border-gray-700 shadow-sm">
          <div className="p-8">
            <div className="space-y-6">
              {toolsData.sectionProjects.map((tool) => (
                <ProjectCard key={tool.name} project={tool} />
              ))}
            </div>
          </div>
        </div>
      )}
    </div>
  );
}

export default function ToolsPage() {
  return (
    <PageLayout title="Tools">
      <div className="max-w-5xl mx-auto px-6 py-12">
        {/* Hero section with new typography */}
        <div className="mb-16 text-center">
          <h1 className="font-serif text-6xl md:text-7xl font-bold mb-6 tracking-tight text-gray-900 dark:text-gray-100">
            Tools
          </h1>
          <p className="text-xl text-[#FF6B4A] dark:text-[#FF8A6B] max-w-2xl mx-auto">
            Utilities for developers, language learners, and makers
          </p>
        </div>

        <SectionNav
          sections={ToolsSections}
          defaultSection="software-development"
        />

        <div className="max-w-5xl mx-auto">
          {ToolsSections.map((section) => (
            <section
              key={section.id}
              id={section.id}
              className="mb-20 scroll-mt-20"
            >
              <div
                className={`relative rounded-lg bg-gradient-to-r ${section.color}/10 to-transparent p-8`}
              >
                <h2
                  className={`font-serif text-4xl font-bold mb-4 bg-gradient-to-r ${section.titleColor} bg-clip-text text-transparent`}
                >
                  {section.name}
                </h2>
                <p className="text-[#FF6B4A] dark:text-[#FF8A6B] mb-12">
                  {section.description}
                </p>
                <SectionContent section={section} />
              </div>
            </section>
          ))}
        </div>
      </div>
    </PageLayout>
  );
}
