# osteele.com Development Roadmap

*Last updated: April 5, 2025*

This document outlines planned improvements and future development for the osteele.com website.

## Site Organization Improvements

### Home Page Structure

- **Topic Integration**: Many of the Topics pages could be subcategories of Software, although some will also have links to course materials and other content. Consider reorganizing these for better discoverability.

- **Professional Section Enhancement**: The Professional category looks unbalanced with only a single module and might need to be more prominent. One idea is to combine it with Teaching & Education or expand it with additional content.

### Content Migration

- **Notion Content Migration**: Move content from external Notion pages into the Astro site:
  - Art Projects (currently at https://osteele.notion.site/art?v=6615e1a7f38845a29859a4b66e9fecf1)
  - Humor/Play content (currently at https://osteele.notion.site/humor?v=a472c71eee21499793ec6a56b99b5ced)

## Technical Improvements

### Data Management

- **Project Data Consolidation**: Ensure all projects are properly represented in the projects.ttl file with appropriate metadata.

- **Category System Review**: Review the categorization system in sections.ts to ensure it accurately represents the site's content organization.

- **Project Type vs. Category Inconsistency**: Fix the mismatch between project types and categories in the filtering system. Currently, web apps are categorized with "webapp" or "web-app" but are assigned the project type "tools", which creates confusion in the UI and filtering logic. Consider refactoring to make the naming more consistent.

- **Project Relationships**: Implement "related projects" or "see also" sections to help users discover connected work across different categories.

- **Project Status Indicators**: Add clear visual indicators for project status:
  - Active: Currently maintained and updated
  - Recent: Updated within the last year but not actively maintained
  - Archived: Historical projects no longer maintained
  - Ensure these statuses are properly represented in the projects.ttl data

### UI/UX Enhancements

- **Navigation Improvements**: Consider adding breadcrumbs or other navigation aids for deeper site sections.

- **Responsive Design Review**: Ensure all pages are fully responsive and provide a good experience on mobile devices.

## Content Development

- **Project Documentation**: Expand documentation for key projects, especially those that are actively maintained.

- **Media Integration**: Add more visual elements (screenshots, diagrams) to project pages.

## Future Features

- **Project Timeline**: Add a visual timeline of projects by year.

- **Technology Tags**: Implement a tag system to filter projects by technology or programming language.

- **Interactive Demos**: Add more interactive demos for applicable projects directly on the website.

- **Project Status Filtering**: Allow users to filter projects by status (active, recent, archived) across all category pages.
