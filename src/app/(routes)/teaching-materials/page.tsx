"use client";

import { PageLayout } from "@/components/page-layout";
import { Tabs, TabsContent, TabsList, TabsTrigger } from "@/components/ui/tabs";
import Link from "next/link";
import { useState } from "react";
import { TeachingBanner } from "@/components/teaching-banner";

interface Subsection {
  name: string;
}

interface Section {
  id: string;
  name: string;
  color: string;
  titleColor: string;
  description: string;
  subsections?: Subsection[];
}

export default function TeachingMaterialsPage() {
  const [activeSection, setActiveSection] = useState("courses");

  const sections: Section[] = [
    {
      id: "courses",
      name: "Course Materials",
      color: "from-amber-500",
      titleColor: "from-amber-500 to-amber-300",
      description: "Course notes and teaching materials.",
      subsections: [
        { name: "Creative Coding" },
        { name: "Physical Computing" },
        { name: "Other Courses" },
      ],
    },
    {
      id: "tools",
      name: "Educational Tools",
      color: "from-blue-500",
      titleColor: "from-blue-500 to-blue-300",
      description: "Interactive tools and visualizations for learning.",
      subsections: [{ name: "Programming Tools" }, { name: "Teaching Tools" }],
    },
  ];

  const scrollToSection = (sectionId: string) => {
    setActiveSection(sectionId);
    const element = document.getElementById(sectionId);
    if (element) {
      element.scrollIntoView({ behavior: "smooth" });
    }
  };

  const getProjectsByCategory = (category: string, subcategory?: string) => {
    if (category === "courses") {
      switch (subcategory) {
        case "Creative Coding":
          return [
            {
              name: "JavaScript and P5.js",
              url: "https://notes.osteele.com/p5js",
              description:
                "JavaScript resources, arrays, p5.js tutorials and examples, and VS Code setup for p5.js",
            },
            {
              name: "PoseNet Resources",
              url: "https://notes.osteele.com/posenet",
              description: "Resources for working with PoseNet in p5.js",
            },
          ];
        case "Physical Computing":
          return [
            {
              name: "Arduino",
              url: "https://notes.osteele.com/arduino",
              description:
                "Arduino programming and physical computing resources",
            },
            {
              name: "Raspberry Pi",
              url: "https://notes.osteele.com/raspberry-pi",
              description: "Raspberry Pi setup and programming guides",
            },
          ];
        case "Other Courses":
          return [
            {
              name: "Woodworking for Art and Design",
              url: "https://notes.osteele.com/courses",
              description: "Course materials for Woodworking at NYU Shanghai",
            },
            {
              name: "Movement Practices",
              url: "https://notes.osteele.com/courses",
              description:
                "Course materials for Movement Practices and Computing",
            },
          ];
      }
    } else if (category === "tools") {
      switch (subcategory) {
        case "Programming Tools":
          return [
            {
              name: "Map Explorer",
              url: "https://osteele.github.io/map-explorer/",
              description:
                "Interactive visualization of the map function in Arduino, Processing, and p5.js",
            },
            {
              name: "PWM Explorer",
              url: "https://osteele.github.io/pwm-explorer/",
              description:
                "Interactive visualization of Pulse Width Modulation",
            },
          ];
        case "Teaching Tools":
          return [
            {
              name: "Course Notes",
              url: "https://notes.osteele.com",
              description: "Collection of course notes and teaching materials",
            },
          ];
      }
    }
    return [];
  };

  return (
    <PageLayout title="Teaching Materials">
      <TeachingBanner />

      <div className="container">
        <Tabs defaultValue="materials" className="w-full">
          <TabsList className="grid w-full grid-cols-2 mb-8 p-1 bg-gray-100/50 dark:bg-gray-800/50 rounded-lg">
            <TabsTrigger
              asChild
              value="teaching"
              className="data-[state=active]:bg-white dark:data-[state=active]:bg-gray-700 data-[state=active]:shadow-sm transition-all"
            >
              <Link href="/teaching" className="px-8 py-3">
                Courses
              </Link>
            </TabsTrigger>
            <TabsTrigger
              asChild
              value="materials"
              className="data-[state=active]:bg-white dark:data-[state=active]:bg-gray-700 data-[state=active]:shadow-sm transition-all"
            >
              <Link href="/teaching-materials" className="px-8 py-3">
                Educational Materials
              </Link>
            </TabsTrigger>
          </TabsList>

          <TabsContent value="materials" className="space-y-8">
            <nav className="sticky top-0 z-50 bg-white/80 dark:bg-gray-900/80 backdrop-blur-sm border-b border-gray-200 dark:border-gray-800">
              <div className="container py-2 relative">
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

            <div className="space-y-6">
              {sections.map((section) => (
                <section
                  key={section.id}
                  id={section.id}
                  className="mb-12 scroll-mt-20"
                >
                  <div
                    className={`relative rounded-lg bg-gradient-to-r ${section.color}/20 to-transparent p-6`}
                  >
                    <h2
                      className={`text-3xl font-bold mb-2 bg-gradient-to-r ${section.color} bg-clip-text text-transparent`}
                    >
                      {section.name}
                    </h2>
                    <p className="text-gray-600 dark:text-gray-400 mb-6 text-lg">
                      {section.description}
                    </p>
                    <div className="grid gap-6 max-w-3xl">
                      {section.subsections?.map((subsection) => (
                        <div
                          key={subsection.name}
                          className="bg-white/70 dark:bg-gray-800/70 rounded-lg backdrop-blur-sm border border-gray-200 dark:border-gray-700 shadow-sm"
                        >
                          <div className="p-6">
                            <h3 className="text-xl font-semibold mb-4 text-gray-800 dark:text-gray-100">
                              {subsection.name}
                            </h3>
                            <div className="space-y-4">
                              {getProjectsByCategory(
                                section.id,
                                subsection.name
                              ).map((project) => (
                                <div key={project.name}>
                                  <Link
                                    href={project.url}
                                    className="text-blue-600 dark:text-blue-400 hover:underline font-medium"
                                  >
                                    {project.name}
                                  </Link>
                                  <p className="text-gray-600 dark:text-gray-300 mt-1">
                                    {project.description}
                                  </p>
                                </div>
                              ))}
                            </div>
                          </div>
                        </div>
                      ))}
                    </div>
                  </div>
                </section>
              ))}
            </div>
          </TabsContent>
        </Tabs>
      </div>
    </PageLayout>
  );
}
