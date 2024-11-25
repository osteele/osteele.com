"use client";

import { PageLayout } from "@/components/page-layout";
import { useState } from "react";
import {
  Section,
  SectionProjects,
  getProjectsByCategory,
} from "@/lib/sections";
import { ProjectCard } from "@/components/project-card";

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

export default function SoftwarePage() {
  const [activeSection, setActiveSection] = useState("software-development");

  const sections: Section[] = Sections;

  const scrollToSection = (sectionId: string) => {
    setActiveSection(sectionId);
    const element = document.getElementById(sectionId);
    if (element) {
      element.scrollIntoView({ behavior: "smooth" });
    }
  };

  const getProjectsForSection = (section: Section): SectionProjects => {
    return getProjectsByCategory(section, "software");
  };

  return (
    <PageLayout title="Software">
      {/* Sticky Navigation */}
      <nav className="sticky top-0 z-50 bg-white/80 dark:bg-gray-900/80 backdrop-blur-sm border-b border-gray-200 dark:border-gray-800">
        <div className="max-w-5xl mx-auto px-4 py-4 relative">
          <div className="flex gap-3 overflow-x-auto hide-scrollbar">
            {sections.map((section) => (
              <button
                key={section.id}
                onClick={() => scrollToSection(section.id)}
                className={`px-3 py-1.5 rounded-full text-sm font-medium whitespace-nowrap transition-colors
                  ${
                    activeSection === section.id
                      ? `bg-gradient-to-r ${section.color} to-transparent text-white`
                      : "text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800"
                  }`}
              >
                {section.name}
              </button>
            ))}
          </div>
          <div className="absolute right-0 top-0 h-full w-20 bg-gradient-to-l from-white dark:from-gray-900 to-transparent pointer-events-none" />
        </div>
      </nav>

      <div className="max-w-5xl mx-auto px-4">
        {sections.map((section) => (
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
              <div className="grid gap-6">
                {section.subsections ? (
                  <>
                    {getProjectsForSection(section).sectionProjects.length >
                      0 && (
                      <div className="bg-white/50 dark:bg-gray-800/50 rounded-lg backdrop-blur-sm border border-gray-200 dark:border-gray-700">
                        <div className="p-6">
                          <div className="space-y-4">
                            {getProjectsForSection(section).sectionProjects.map(
                              (project) => (
                                <ProjectCard
                                  key={project.name}
                                  project={project}
                                />
                              )
                            )}
                          </div>
                        </div>
                      </div>
                    )}
                    {section.subsections.map((subsection) => {
                      const subsectionProjects =
                        getProjectsForSection(section).subsectionProjects.get(
                          subsection.name
                        ) || [];
                      if (subsectionProjects.length === 0) return null;

                      return (
                        <div
                          key={subsection.name}
                          className="bg-white/50 dark:bg-gray-800/50 rounded-lg backdrop-blur-sm border border-gray-200 dark:border-gray-700"
                        >
                          <div className="p-6">
                            <h3
                              className={`text-xl font-semibold mb-4 text-${section.color.replace("from-", "")}-700 dark:text-${section.color.replace("from-", "")}-300`}
                            >
                              {subsection.name}
                            </h3>
                            <div className="space-y-4">
                              {subsectionProjects.map((project) => (
                                <ProjectCard
                                  key={project.name}
                                  project={project}
                                />
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
                        {getProjectsForSection(section).sectionProjects.map(
                          (project) => (
                            <ProjectCard key={project.name} project={project} />
                          )
                        )}
                      </div>
                    </div>
                  </div>
                )}
              </div>
            </div>
          </section>
        ))}
      </div>
    </PageLayout>
  );
}
