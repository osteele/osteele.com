# osteele.com Technical Documentation

*Last updated: April 5, 2025*

This document provides technical details about the osteele.com website architecture, data management, and implementation.

## Project Structure

The website is built with Astro and uses TypeScript, React components, and Tailwind CSS for styling. The project follows the standard Astro project structure:

```
/src
  /components  # Reusable UI components
  /data        # Data files and API helpers
  /layouts     # Page layouts and templates
  /lib         # Utility functions
  /pages       # Page routes
  /styles      # Global styles
/public        # Static assets
```

## Data Management

### Project Data Source

The primary data source for projects is the `projects.ttl` file located in `/src/data/`. This file uses the Turtle (TTL) RDF format to define structured data about each project, including:

- Title and description
- Repository URL and website URL
- Categories and tags
- Creation and modification dates
- Primary programming language
- Archive status

Example project entry in TTL format:

```ttl
os:project-name a doap:Project ;
    dc:title "Project Title" ;
    doap:repository "https://github.com/osteele/project-name" ;
    dc:description "Project description." ;
    os:category "category-1", "category-2" ;
    schema:dateCreated "2023-01-01T00:00:00Z"^^xsd:dateTime ;
    schema:dateModified "2023-12-31T00:00:00Z"^^xsd:dateTime ;
    os:primaryLanguage "JavaScript" ;
    os:isArchived false ;
    os:topics "topic-1", "topic-2" .
```

### Project Data Loading

The TTL data is loaded and processed by the `loadProjectsFromTurtle()` function in `/src/data/projects.ts`. This function:

1. Parses the TTL file
2. Converts the RDF data into JavaScript objects
3. Organizes projects by their categories
4. Makes the data available to Astro pages and components

### Category and Section Definitions

The categorization system is defined in `/src/data/sections.ts`. This file contains arrays of section definitions used throughout the site:

- `WebAppSections`: Defines sections for the Tools page
- `SoftwareSections`: Defines sections for the Software page

Each section definition includes:

- ID and name
- Color scheme
- Description
- Associated categories
- Optional subsections

## Page Generation

### Filtering Projects for Display

Projects are filtered for display on various pages using the `getProjectsByCategory()` function from `/src/lib/sections.ts`. This function:

1. Takes a section definition and project type
2. Filters the project list to match the specified categories
3. Organizes projects into main section projects and subsection projects
4. Returns the filtered and organized project data

Example usage in a page:

```js
const projectData = getProjectsByCategory(section, "webapp", projects);
```

### External Content Integration

Some project categories (Art Projects, Humor) currently link to external Notion pages. As noted in the roadmap, there are plans to migrate this content into the Astro site.

## Component System

The site uses several key components for displaying projects:

- `ProjectCard.astro`: Renders individual project information
- `SectionNav.astro`: Provides navigation between sections
- `PageLayout.astro`: Provides consistent page structure

## Build and Deployment

The site uses Bun for package management and includes the following commands:

- `bun run dev`: Start Astro development server
- `bun run build`: Build app (includes favicon generation)
- `bun run build:prod`: Production build
- `bun run preview`: Preview built site locally
- `bun run lint`: Run biome
- `bun run fix`: Fix linting issues with biome
- `bun run typecheck`: Run TypeScript type checking
- `bun run test`: Run all tests

## Future Technical Improvements

See the roadmap document for planned technical improvements and feature additions.
