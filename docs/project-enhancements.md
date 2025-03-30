# Project Display Enhancements

*Last updated: April 5, 2025*

This document outlines planned enhancements to the project display system on osteele.com, focusing on project status indicators, related projects, and filtering capabilities.

## Project Status System

### Data Model Updates

The `projects.ttl` file will be enhanced to include a status field for each project:

```ttl
os:project-name a doap:Project ;
    # Existing fields...
    os:status "active" ; # One of: "active", "recent", "archived"
```

### Status Definitions

- **Active**: Currently maintained and updated projects
- **Recent**: Updated within the last year but not actively maintained
- **Archived**: Historical projects no longer maintained

### Implementation

1. **Data Loading**: Update the `loadProjectsFromTurtle()` function in `src/data/projects.ts` to extract the status field:

```javascript
const status = getLiteralValue(subjectStr, `${OS}status`) || "archived";

return {
  // Existing fields...
  status,
};
```

2. **ProjectCard Component**: Enhance the ProjectCard component to display status badges with appropriate styling:

```typescript
// Status badge color mapping
const statusColors = {
  active: "bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-300",
  recent: "bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-300",
  archived: "bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-400"
};

// Status label mapping
const statusLabels = {
  active: "Active",
  recent: "Recent",
  archived: "Archived"
};
```

3. **Status Badge Display**:

```html
<span class={`shrink-0 px-2 py-1 text-xs font-medium rounded-full ${statusColors[project.status || "archived"]}`}>
  {statusLabels[project.status || "archived"]}
</span>
```

## Related Projects

### Data Model Updates

The `projects.ttl` file will be enhanced to include related projects:

```ttl
os:project-name a doap:Project ;
    # Existing fields...
    os:relatedProjects "project1", "project2" ; # References to other projects
```

### Implementation

1. **Data Loading**: Update the `loadProjectsFromTurtle()` function to extract related projects:

```javascript
const relatedProjects = getAllValues(subjectStr, `${OS}relatedProjects`);

return {
  // Existing fields...
  relatedProjects,
};
```

2. **ProjectCard Component**: Add a section to display related projects:

```typescript
// Get related projects
const relatedProjectsData = project.relatedProjects 
  ? allProjects.filter(p => project.relatedProjects.includes(p.name))
  : [];
```

```html
<!-- Related projects section (if any) -->
{relatedProjectsData.length > 0 && (
  <div class="mt-4 pt-4 border-t border-gray-200 dark:border-gray-700">
    <h4 class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Related Projects</h4>
    <div class="flex flex-wrap gap-2">
      {relatedProjectsData.map(related => (
        <a 
          href={`#${related.name.toLowerCase().replace(/\s+/g, '-')}`}
          class="text-xs px-2 py-1 rounded-full bg-gray-100 dark:bg-gray-800 text-gray-700 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-700 transition-colors"
        >
          {related.name}
        </a>
      ))}
    </div>
  </div>
)}
```

## Project Filtering Controls

### Filter UI Component

Create a new component for filtering projects by status:

```typescript
// src/components/ProjectFilter.astro
---
const { activeFilter = "all" } = Astro.props;

const filters = [
  { id: "all", label: "All" },
  { id: "active", label: "Active" },
  { id: "recent", label: "Recent" },
  { id: "archived", label: "Archived" }
];
---

<div class="mb-8">
  <div class="flex flex-wrap gap-2">
    {filters.map(filter => (
      <button 
        id={`filter-${filter.id}`} 
        class={`px-3 py-1 rounded-full ${activeFilter === filter.id ? 'bg-gray-200 dark:bg-gray-700' : ''}`}
      >
        {filter.label}
      </button>
    ))}
  </div>
</div>

<script>
  // Client-side filtering logic
  const buttons = document.querySelectorAll('button[id^="filter-"]');
  const projectCards = document.querySelectorAll('.project-card');
  
  buttons.forEach(button => {
    button.addEventListener('click', () => {
      // Update active button
      buttons.forEach(b => b.classList.remove('bg-gray-200', 'dark:bg-gray-700'));
      button.classList.add('bg-gray-200', 'dark:bg-gray-700');
      
      const filter = button.id.replace('filter-', '');
      
      // Filter projects
      projectCards.forEach(card => {
        if (filter === 'all') {
          card.classList.remove('hidden');
        } else {
          const status = card.getAttribute('data-status');
          if (status === filter) {
            card.classList.remove('hidden');
          } else {
            card.classList.add('hidden');
          }
        }
      });
    });
  });
</script>
```

### Integration

Include the filter component in the project list pages:

```html
<ProjectFilter activeFilter="all" />
<ProjectList 
  sections={sections} 
  projects={filteredProjects} 
  projectType="webapp" 
  showTypeLabels={true} 
/>
```

## Architecture Considerations

### Breadcrumbs Component

The breadcrumbs should be implemented as a separate component rather than being part of the CategoryLayout. This allows for reuse across all pages, not just category pages:

```typescript
// src/components/Breadcrumbs.astro
---
export interface BreadcrumbItem {
  name: string;
  href: string;
}

const { items = [] } = Astro.props;
---

<nav class="flex items-center gap-2 text-gray-600 dark:text-gray-400 mb-8">
  {items.map((item, index) => (
    <>
      {index > 0 && <span>/</span>}
      {index === items.length - 1 ? (
        <span class="text-gray-900 dark:text-gray-100">{item.name}</span>
      ) : (
        <a href={item.href} class="hover:text-gray-900 dark:hover:text-gray-100">
          {item.name}
        </a>
      )}
    </>
  ))}
</nav>
```

This component can then be used in both the CategoryLayout and other page layouts.

### Project Category Normalization

To normalize project categories like "web-app", "web-apps", and "webapp", we should implement a normalization function in `src/data/projects.ts` rather than handling this in each page:

```typescript
// In projects.ts or a new projects.ts file

// Category normalization map
const CATEGORY_NORMALIZATIONS = {
  "web-app": "webapp",
  "web-apps": "webapp",
  "command-line": "cli",
  "command-line-tool": "cli",
  // Add other normalizations as needed
};

// Normalize categories during project loading
export function normalizeCategories(categories) {
  const normalizedSet = new Set();
  
  categories.forEach(category => {
    // Add the original category
    normalizedSet.add(category);
    
    // Add the normalized version if it exists
    if (CATEGORY_NORMALIZATIONS[category]) {
      normalizedSet.add(CATEGORY_NORMALIZATIONS[category]);
    }
  });
  
  return Array.from(normalizedSet);
}

// Update in loadProjectsFromTurtle
const categories = normalizeCategories(getAllValues(subjectStr, `${OS}category`));
```

With this approach, each project will have both its original categories and the normalized versions, simplifying filtering in page components.

## Implementation Plan

1. **Data Model Updates**:
   - Update the TTL schema to include status and related projects
   - Add these fields to existing project entries

2. **Component Development**:
   - Create the ProjectFilter component
   - Update the ProjectCard component
   - Create the Breadcrumbs component

3. **Data Processing**:
   - Implement category normalization
   - Update the data loading functions

4. **Integration**:
   - Add the new components to page templates
   - Test filtering functionality

This implementation will be done as a separate stage from the main site organization changes.
