import { Section } from "@/lib/sections";

export const ToolsSections: Section[] = [
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

export const SoftwareSections: Section[] = [
  {
    id: "web-tools",
    name: "Web & Publishing",
    color: "from-amber-500",
    titleColor: "from-amber-500 to-amber-300",
    description: "Web Publishing & Documentation",
    categories: ["web-publishing", "documentation-tools", "web-technologies"],
  },
  // {
  //   id: "software-development",
  //   name: "Software Development",
  //   color: "from-amber-500",
  //   titleColor: "from-amber-500 to-amber-300",
  //   description: "Development Libraries & Tools",
  //   categories: ["development-tools", "testing-tools"],
  // },
  {
    id: "language-learning",
    name: "Language Learning",
    color: "from-sky-500",
    titleColor: "from-sky-500 to-sky-300",
    description: "Language Learning Tools",
    categories: ["language-learning"],
  },
  {
    id: "machine-embroidery",
    name: "Machine Embroidery",
    color: "from-pink-500",
    titleColor: "from-pink-500 to-pink-300",
    description: "Machine Embroidery Tools",
    categories: ["machine-embroidery"],
  },
  {
    id: "classroom-tools",
    name: "Classroom Tools",
    color: "from-green-500",
    titleColor: "from-green-500 to-green-300",
    description: "Teaching & Course Management",
    categories: ["education", "student-tools", "educator-tools"],
  },
  {
    id: "llm-tools",
    name: "LLM Tools & Libraries",
    color: "from-rose-500",
    titleColor: "from-rose-500 to-rose-300",
    description: "Large Language Model Tools",
    categories: ["llm-tools", "llm-libraries"],
  },
  {
    id: "p5js",
    name: "p5.js Tools & Libraries",
    color: "from-blue-500",
    titleColor: "from-blue-500 to-blue-300",
    description: "p5.js Development Tools",
    categories: ["p5js-tools", "p5js-libraries"],
  },
  {
    id: "physical-computing",
    name: "Physical Computing",
    color: "from-purple-500",
    titleColor: "from-purple-500 to-purple-300",
    description: "Microcontroller & Sensor Tools",
    categories: ["physical-computing"],
  },
  {
    id: "legacy-libraries",
    name: "Legacy Libraries",
    color: "from-gray-500",
    titleColor: "from-gray-500 to-gray-300",
    description: "Historical JavaScript & Ruby Libraries",
    categories: ["javascript-libraries", "ruby-libraries", "rails-plugins"],
  },
];
