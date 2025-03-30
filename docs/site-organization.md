# osteele.com Site Organization

*Last updated: April 5, 2025*

This document describes the organization of the osteele.com website, including the home page structure and the organization of project pages.

## Home Page Organization

The home page is organized into five primary categories, each containing several modules that link to different sections of the site.

### Primary Categories

1. **Software**
   - Web Apps
   - Command Line Tools
   - Libraries & Frameworks

2. **Teaching & Education**
   - Courses Taught
   - Educational Software
   - Course Materials

3. **Creative Works**
   - Photography
   - Woodworking
   - Art
   - Play (Humor)

4. **Professional**
   - Products

5. **Topics**
   - Collections across different areas

## Project Pages Organization

### Web Apps

The Web Apps section is organized into the following subcategories:

1. **Language Learning**
   - Links to the Language Learning topics page (https://osteele.com/tools)

2. **Art Projects**
   - Links to external Notion page (https://osteele.notion.site/art?v=6615e1a7f38845a29859a4b66e9fecf1)

3. **Humor**
   - Links to external Notion page (https://osteele.notion.site/humor?v=a472c71eee21499793ec6a56b99b5ced)

### Command Line Tools

The Command Line Tools section is organized into the following subcategories:

1. **Language Learning**
   - Tools related to language learning and translation

2. **Classroom Tools**
   - Tools for educational purposes and classroom management

3. **Machine Embroidery**
   - Command line utilities for machine embroidery workflows

4. **Other Tools**
   - Miscellaneous command line utilities that don't fit into other categories

### Libraries & Frameworks

The Libraries & Frameworks section is organized into the following subcategories:

1. **Web Publishing**
   - Libraries related to web development and publishing

2. **Language Learning**
   - Libraries that support language learning applications

3. **LLM Tools**
   - Libraries for working with Large Language Models

4. **p5.js**
   - Libraries that extend the p5.js creative coding framework

5. **Physical Computing**
   - Libraries for working with microcontrollers and physical computing

## Other Site Pages

### Software Page

The Software page serves as a hub for all software-related projects and is organized into the following categories:

- Web Apps
- Command Line Tools
- Libraries & Frameworks
- P5.js Tools
- Language Learning
- Physical Computing
- Machine Embroidery

It also features a "Recent Projects" section highlighting notable recent work.

### Tools Page

The Tools page focuses on web applications and is organized into these sections:

- Software Development
  - Web Publishing
- Language Learning
- LLM Tools
- Machine Embroidery
- p5.js Tools & Libraries
- Physical Computing
- Education Tools
  - For Students
  - For Educators

### Topic Pages

Topic pages collect projects across different categories related to a specific theme:

- Language Learning
- Physical Computing
- p5.js
- Educational Software
- Teaching Materials
- Photography
- Woodworking

### Other Content Pages

- 404 Page (Custom error page)

## Implementation Notes

The site is built with Astro and uses a component-based architecture. The primary data source for projects is a Turtle (TTL) file that contains structured data about each project, including categories, descriptions, and metadata.

The categorization system is defined in `src/data/sections.ts`, which contains arrays of section definitions used throughout the site.

The home page layout is defined in `src/pages/index.astro`, with primary categories and their child categories specified in arrays at the top of the file.
