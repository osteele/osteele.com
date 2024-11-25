"use client";

import { PageLayout } from "@/components/page-layout";
import { useState } from "react";
import {
  Section,
  Subsection,
  getProjectsByCategory,
  SectionProjects,
} from "@/lib/sections";
import { ProjectCard } from "@/components/project-card";
import { Project } from "@/data/projects";

const Sections: Section[] = [
  {
    id: "software-development",
    name: "Software Development",
    color: "from-amber-500",
    titleColor: "from-amber-500 to-amber-300",
    description:
      "Tools for web publishing, development workflows, and code generation.",
    categories: ["software-development"],
    subsections: [{ name: "Web Publishing", categories: ["web-publishing"] }],
  },
  {
    id: "language-learning",
    name: "Language Learning",
    color: "from-sky-500",
    titleColor: "from-sky-500 to-sky-300",
    description: "Tools to assist in learning foreign languages.",
    categories: ["language-learning"],
  },
  {
    id: "llm-tools",
    name: "LLM Tools",
    color: "from-rose-500",
    titleColor: "from-rose-500 to-rose-300",
    description:
      "Utilities for working with Large Language Models and their outputs.",
    categories: ["llm-tools"],
  },
  {
    id: "machine-embroidery",
    name: "Machine Embroidery",
    color: "from-pink-500",
    titleColor: "from-pink-500 to-pink-300",
    description: "File conversion and automation tools for machine embroidery.",
    categories: ["machine-embroidery"],
  },
  {
    id: "p5js",
    name: "p5.js Tools & Libraries",
    color: "from-blue-500",
    titleColor: "from-blue-500 to-blue-300",
    description:
      "Development tools and libraries for the p5.js creative coding framework.",
    categories: ["p5js"],
  },
  {
    id: "physical-computing",
    name: "Physical Computing",
    color: "from-purple-500",
    titleColor: "from-purple-500 to-purple-300",
    description: "Tools for working with microcontrollers and sensor data.",
    categories: ["physical-computing"],
  },
  {
    id: "education",
    name: "Education Tools",
    color: "from-green-500",
    titleColor: "from-green-500 to-green-300",
    description:
      "Tools for students and educators in computer science and physical computing.",
    categories: ["education"],
    subsections: [
      { name: "For Students", categories: ["student-tools"] },
      { name: "For Educators", categories: ["educator-tools"] },
    ],
  },
];

export default function ToolsPage() {
  const [activeSection, setActiveSection] = useState("software-development");

  const sections: Section[] = Sections;

  const scrollToSection = (sectionId: string) => {
    setActiveSection(sectionId);
    const element = document.getElementById(sectionId);
    if (element) {
      element.scrollIntoView({ behavior: "smooth" });
    }
  };

  const getToolsForSection = (section: Section): SectionProjects => {
    return getProjectsByCategory(section, "tools");
  };

  return (
    <PageLayout title="Tools">
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
              className={`relative rounded-lg bg-gradient-to-r ${section.color}/20 to-transparent p-8`}
            >
              <h2
                className={`text-4xl font-bold mb-3 bg-gradient-to-r ${section.color} bg-clip-text text-transparent`}
              >
                {section.name}
              </h2>
              <p className="text-gray-600 dark:text-gray-400 mb-8 text-lg">
                {section.description}
              </p>
              <div className="grid gap-8">
                {section.subsections ? (
                  <>
                    {getToolsForSection(section).sectionProjects.length > 0 && (
                      <div className="bg-white/70 dark:bg-gray-800/70 rounded-lg backdrop-blur-sm border border-gray-200 dark:border-gray-700 shadow-sm">
                        <div className="p-8">
                          <div className="space-y-6">
                            {getToolsForSection(section).sectionProjects.map(
                              (tool) => (
                                <ProjectCard key={tool.name} project={tool} />
                              )
                            )}
                          </div>
                        </div>
                      </div>
                    )}
                    {section.subsections.map((subsection) => {
                      const subsectionTools =
                        getToolsForSection(section).subsectionProjects.get(
                          subsection.name
                        ) || [];
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
                        {getToolsForSection(section).sectionProjects.map(
                          (tool) => (
                            <ProjectCard key={tool.name} project={tool} />
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
