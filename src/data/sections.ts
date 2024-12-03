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
    id: "web-technologies",
    name: "Web Technologies",
    color: "from-teal-500",
    titleColor: "from-teal-500 to-teal-300",
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
