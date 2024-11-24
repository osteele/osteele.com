export interface Project {
  name: string;
  repo?: string;
  website?: string;
  description: string;
  categories: string[];
}

export interface ProjectsData {
  projects: Project[];
}

export const projectsData: ProjectsData = {
  projects: [
    // Web Publishing
    {
      name: "Liquid Template Engine",
      repo: "https://github.com/osteele/liquid",
      description: "A pure Go implementation of Shopify Liquid templates.",
      categories: ["software", "software-development", "web-publishing"],
    },
    {
      name: "Gojekyll",
      repo: "https://github.com/osteele/gojekyll",
      description: "A fast Go implementation of the Jekyll blogging engine.",
      categories: ["tools", "software-development", "web-publishing"],
    },
    {
      name: "Scrollshot2PDF",
      repo: "https://github.com/osteele/scrollshot2pdf",
      description:
        "Convert tall screenshots into multi-page PDFs with intelligent page breaks.",
      categories: ["tools", "software-development", "web-publishing"],
    },

    // LLM Tools
    {
      name: "Claude Chat Viewer",
      repo: "https://github.com/osteele/claude-chat-viewer",
      website: "https://tools.osteele.com/claude-chat-viewer",
      description:
        "View Claude chat conversations from exported JSON files. Copy the chat as RTF; download a zip of all the artifacts.",
      categories: ["tools", "llm-tools"],
    },
    {
      name: "Claude Artifact Unpacker",
      repo: "https://github.com/osteele/claude-artifact-unpacker",
      description:
        "Unpack and organize multi-file projects from Claude's Artifacts.",
      categories: ["tools", "llm-tools"],
    },
    {
      name: "Prompt Matrix (JS)",
      repo: "https://github.com/osteele/prompt-matrix.js",
      description: "JavaScript library for expanding prompt matrix strings.",
      categories: ["software", "llm-tools"],
    },
    {
      name: "Prompt Matrix (Python)",
      repo: "https://github.com/osteele/prompt-matrix.py",
      description: "Python package for expanding prompt matrix strings.",
      categories: ["software", "llm-tools"],
    },

    // Language Learning
    {
      name: "Shutterspeak",
      website: "https://tools.osteele.com/shutterspeak",
      description: "Language learning with photo descriptions.",
      categories: ["tools", "language-learning"],
    },
    {
      name: "Labelingo",
      repo: "https://github.com/osteele/labelingo",
      description:
        "Annotate UI screenshots with translations for language learning.",
      categories: ["tools", "language-learning"],
    },
    {
      name: "Kana Game",
      repo: "https://github.com/osteele/kana-game",
      website: "https://tools.osteele.com/kana-game",
      description: "An interactive game for learning Japanese kana characters.",
      categories: ["tools", "language-learning"],
    },

    // Machine Embroidery
    {
      name: "Stitch Sync",
      repo: "https://github.com/osteele/stitch-sync",
      description:
        "Watch and convert embroidery files to machine-compatible formats.",
      categories: ["tools", "machine-embroidery"],
    },
    {
      name: "Pyembroidery Convert",
      repo: "https://github.com/osteele/pyembroidery-convert",
      description: "CLI tool for converting between embroidery file formats.",
      categories: ["tools", "machine-embroidery"],
    },

    // p5.js Tools & Libraries
    {
      name: "p5 Server",
      repo: "https://github.com/osteele/p5-server",
      description:
        "Command-line tool that runs p5.js sketches with live reload and automatic library inclusion.",
      categories: ["tools", "p5js", "development-tools"],
    },
    {
      name: "P5 Server VSCode Extension",
      repo: "https://github.com/osteele/p5-server-vscode",
      description:
        "Create and run p5.js sketches within Visual Studio Code with integrated development server and browser.",
      categories: ["tools", "p5js", "development-tools"],
    },
    {
      name: "p5.layers",
      repo: "https://github.com/osteele/p5.layers",
      description:
        "Simplifies use of createGraphics and p5.js Renders objects for drawing layers.",
      categories: ["software", "p5js", "libraries"],
    },
    {
      name: "p5.rotate-about",
      repo: "https://github.com/osteele/p5.rotate-about",
      description:
        "Adds rotateAbout() and scaleAbout() functions for rotating and scaling around a point.",
      categories: ["software", "p5js", "libraries"],
    },
    {
      name: "p5.vector-arguments",
      repo: "https://github.com/osteele/p5.vector-arguments",
      description:
        "Modifies p5.js Shape functions to accept p5.Vector instances as arguments.",
      categories: ["software", "p5js", "libraries"],
    },

    // Physical Computing
    {
      name: "IMU Tools",
      repo: "https://github.com/osteele/imu-tools",
      description:
        "Tools for sending IMU data from ESP32 and receiving it via MQTT or Bluetooth.",
      categories: ["tools", "physical-computing"],
    },
    {
      name: "Arduino-BLE-IMU",
      repo: "https://github.com/osteele/Arduino-BLE-IMU",
      description:
        "ESP32 firmware for relaying BNO055 data wirelessly via MQTT and Bluetooth.",
      categories: ["software", "physical-computing"],
    },
    {
      name: "IMU Client Examples",
      repo: "https://github.com/osteele/imu-client-examples",
      description:
        "Examples using wireless IMU data to animate 3D models and create data visualizations.",
      categories: ["software", "physical-computing"],
    },

    // Education Tools
    {
      name: "Map Explorer",
      repo: "https://osteele.github.io/map-explorer/",
      description:
        "Interactive visualization of the map function in Arduino, Processing, and p5.js.",
      categories: ["tools", "education", "student-tools"],
    },
    {
      name: "PWM Explorer",
      repo: "https://osteele.github.io/pwm-explorer/",
      description: "Interactive visualization of Pulse Width Modulation (PWM).",
      categories: ["tools", "education", "student-tools"],
    },
    {
      name: "Callgraph",
      repo: "https://github.com/osteele/callgraph",
      description:
        "Jupyter notebook extension that adds call graphs to functions.",
      categories: ["tools", "education", "educator-tools"],
    },
    {
      name: "Section Wheel",
      repo: "https://github.com/osteele/section-wheel",
      description:
        "Interactive wheel for selecting student presentation order.",
      categories: ["tools", "education", "educator-tools"],
    },
    {
      name: "Multiclone",
      repo: "https://github.com/osteele/multiclone",
      description:
        "Fast tool for cloning all forks of a repository or GitHub Classroom assignments.",
      categories: ["tools", "education", "educator-tools"],
    },
    {
      name: "nbcollate",
      repo: "https://github.com/osteele/nbcollate",
      description:
        "Combines multiple student Jupyter notebooks into a single organized notebook.",
      categories: ["tools", "education", "educator-tools"],
    },
    {
      name: "Assignment Dashboard",
      repo: "https://github.com/osteele/assignment-dashboard",
      description:
        "Dashboard for tracking student Jupyter notebook submissions on GitHub.",
      categories: ["tools", "education", "educator-tools"],
    },

    // Education & Teaching Materials
    {
      name: "Discrete Math",
      repo: "https://github.com/osteele/discrete-math",
      description: "Course materials for Discrete Mathematics.",
      categories: ["education", "teaching-materials", "math"],
    },
    {
      name: "Computation and Programming",
      repo: "https://github.com/osteele/computation-and-programming-course",
      description:
        "Course materials for Introduction to Computation and Programming.",
      categories: ["education", "teaching-materials", "programming"],
    },
    {
      name: "Physical Computing",
      repo: "https://github.com/osteele/physical-computing-course",
      description: "Course materials for Physical Computing.",
      categories: ["education", "teaching-materials", "physical-computing"],
    },
    {
      name: "Design for Manufacturing",
      repo: "https://github.com/osteele/dfm-course",
      description: "Course materials for Design for Manufacturing.",
      categories: ["education", "teaching-materials", "manufacturing"],
    },
    {
      name: "Interactive Installation",
      repo: "https://github.com/osteele/interactive-installation-course",
      description: "Course materials for Interactive Installation.",
      categories: ["education", "teaching-materials", "installation-art"],
    },
    {
      name: "Generative Art",
      repo: "https://github.com/osteele/generative-art-course",
      description: "Course materials for Generative Art.",
      categories: ["education", "teaching-materials", "art"],
    },
    {
      name: "Creative Coding",
      repo: "https://github.com/osteele/creative-coding-course",
      description: "Course materials for Creative Coding.",
      categories: ["education", "teaching-materials", "programming", "art"],
    },
    {
      name: "Processing and Arduino",
      repo: "https://github.com/osteele/processing-arduino-course",
      description:
        "Course materials for Programming with Processing and Arduino.",
      categories: [
        "education",
        "teaching-materials",
        "programming",
        "physical-computing",
      ],
    },
    {
      name: "Matrix Multiplication",
      repo: "https://github.com/osteele/matrix-multiplication",
      description: "Interactive visualization of matrix multiplication.",
      categories: ["education", "teaching-materials", "math", "visualization"],
    },
    {
      name: "Wiring Diagram Generator",
      repo: "https://github.com/osteele/wiring-diagram-generator",
      description: "Generate wiring diagrams for Arduino projects.",
      categories: [
        "education",
        "teaching-materials",
        "physical-computing",
        "tools",
      ],
    },
  ],
} as const;

export default projectsData;
