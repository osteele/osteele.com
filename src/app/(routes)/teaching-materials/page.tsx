"use client";

import { PageLayout } from "@/components/page-layout";
import { Tabs, TabsContent, TabsList, TabsTrigger } from "@/components/ui/tabs";
import Link from "next/link";
import { TeachingBanner } from "@/components/teaching-banner";
import { SectionNav } from "@/components/section-nav";

interface Project {
  name: string;
  url: string;
  description: string;
}

interface Section {
  id: string;
  name: string;
  color: string;
  titleColor: string;
  description: string;
  subsections?: { name: string }[];
}

const Sections: Section[] = [
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

function getProjectsByCategory(
  category: string,
  subcategory?: string
): Project[] {
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
            description: "Arduino programming and physical computing resources",
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
            description: "Interactive visualization of Pulse Width Modulation",
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
}

function SectionContent({ section }: { section: Section }) {
  return (
    <div className="grid gap-6 max-w-3xl">
      {section.subsections?.map((subsection) => {
        const projects = getProjectsByCategory(section.id, subsection.name);
        if (projects.length === 0) return null;

        return (
          <div
            key={subsection.name}
            className="bg-white/70 dark:bg-gray-800/70 rounded-lg backdrop-blur-sm border border-gray-200 dark:border-gray-700 shadow-sm"
          >
            <div className="p-6">
              <h3 className="text-xl font-semibold mb-4 text-gray-800 dark:text-gray-100">
                {subsection.name}
              </h3>
              <div className="space-y-4">
                {projects.map((project) => (
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
        );
      })}
    </div>
  );
}

export default function TeachingMaterialsPage() {
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
            <SectionNav sections={Sections} defaultSection="courses" />

            <div className="space-y-6">
              {Sections.map((section) => (
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
                    <SectionContent section={section} />
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
