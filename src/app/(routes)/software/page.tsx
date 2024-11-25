import { PageLayout } from "@/components/page-layout";
import {
  Section,
  SectionProjects,
  getProjectsByCategory,
} from "@/lib/sections";
import { ProjectCard } from "@/components/project-card";
import { SectionNav } from "@/components/section-nav";

const Sections: Section[] = [
  {
    id: "web-technologies",
    name: "Web Technologies",
    color: "from-emerald-500",
    titleColor: "from-emerald-500 to-emerald-300",
    description:
      "Tools and infrastructure for web application deployment and routing.",
    subsections: [
      { name: "Web Publishing", categories: ["web-publishing"] },
      { name: "Routing", categories: ["routing"] },
    ],
  },
  {
    id: "software-development",
    name: "Software Development",
    color: "from-amber-500",
    titleColor: "from-amber-500 to-amber-300",
    description: "Libraries and applications for software development.",
  },
  {
    id: "llm-libraries",
    name: "LLM Libraries",
    color: "from-rose-500",
    titleColor: "from-rose-500 to-rose-300",
    description: "Libraries for working with Large Language Models.",
  },
  {
    id: "p5js",
    name: "p5.js Tools & Libraries",
    color: "from-blue-500",
    titleColor: "from-blue-500 to-blue-300",
    description: "Libraries that extend the p5.js creative coding framework.",
    subsections: [{ name: "Libraries" }],
  },
  {
    id: "physical-computing",
    name: "Physical Computing",
    color: "from-purple-500",
    titleColor: "from-purple-500 to-purple-300",
    description: "Software for microcontrollers and sensor data visualization.",
  },
];

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
      <SectionNav sections={Sections} defaultSection="software-development" />

      <div className="max-w-5xl mx-auto px-4">
        {Sections.map((section) => (
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
