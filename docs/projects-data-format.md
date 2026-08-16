# Projects Data Format (projects.ttl)

This document describes the RDF/Turtle format used in `src/data/projects.ttl` for defining project metadata.

## Overview

The projects data is stored in [Turtle format](https://www.w3.org/TR/turtle/) (`.ttl`), which is a human-readable RDF serialization. Each project is defined as an RDF resource with various properties describing its metadata.

## Namespaces

The following namespace prefixes are used:

```turtle
@prefix dc: <http://purl.org/dc/terms/> .          # Dublin Core terms
@prefix doap: <http://usefulinc.com/ns/doap#> .    # Description of a Project
@prefix foaf: <http://xmlns.com/foaf/0.1/> .       # Friend of a Friend
@prefix schema: <http://schema.org/> .             # Schema.org vocabulary
@prefix os: <http://osteele.com/ns/> .             # Custom namespace
@prefix xsd: <http://www.w3.org/2001/XMLSchema#> . # XML Schema datatypes
```

## Project Structure

Each project is defined with a unique identifier and type declaration:

```turtle
os:project-name a doap:Project ;
    # properties...
```

## Core Properties

### Required Properties

- **`dc:title`** - The display name of the project
  ```turtle
  dc:title "Project Name" ;
  ```

- **`dc:description`** - A brief description of the project
  ```turtle
  dc:description "A tool that does something useful." ;
  ```

- **`os:category`** - Categories for organizing projects (comma-separated). Values
  must come from the vocabulary in `src/data/categories.ts` — see
  [Categories](#categories) below.
  ```turtle
  os:category "web-app", "development-tools" ;
  ```

### Optional Properties

#### URLs and Repository

- **`doap:repository`** - GitHub repository URL
  ```turtle
  doap:repository "https://github.com/username/repo" ;
  ```

- **`schema:url`** - Project website or demo URL
  ```turtle
  schema:url "https://example.com/project" ;
  ```

#### Dates

- **`schema:dateCreated`** - Creation date (ISO 8601 format)
  ```turtle
  schema:dateCreated "2024-01-15T10:30:00Z" ;
  ```

- **`schema:dateModified`** - Last modification date
  ```turtle
  schema:dateModified "2024-11-20T15:45:00Z" ;
  ```

#### Language and Topics

- **`os:primaryLanguage`** - Main programming language
  ```turtle
  os:primaryLanguage "TypeScript" ;
  ```

- **`os:topics`** - Related topics or tags
  ```turtle
  os:topics "machine-learning", "data-visualization" ;
  ```

#### Status and Visibility

- **`os:isArchived`** - Whether the project is archived (default: false)
  ```turtle
  os:isArchived true ;
  ```

- **`os:includeInPortfolio`** - Whether to show in portfolio (default: true)
  ```turtle
  os:includeInPortfolio false ;  # Hides the project
  ```

- **`os:Status`** - Project status (e.g., "Archived")
  ```turtle
  os:Status "Archived" ;
  ```

#### Media

- **`schema:thumbnail`** - Thumbnail image URL (can have multiple)
  ```turtle
  schema:thumbnail "https://images.example.com/thumbnail.webp" ;
  ```

#### Documentation

- **`os:exampleUsage`** - Code examples showing how to use the project
  ```turtle
  os:exampleUsage """# Install the package
  npm install my-package

  # Basic usage
  import { feature } from 'my-package';
  
  const result = feature({ option: true });
  """ ;
  ```

#### Contributions

For projects you've contributed to (not authored):

```turtle
os:contribution [
    os:contributionDescription "Added feature X to the project" ;
    os:pullRequest "https://github.com/org/repo/pull/123" ;
    os:features "Feature 1", "Feature 2", "Feature 3"
] ;
```

## Categories

Categories are the site's only navigation primitive: a project appears on a page
because one of its categories matches a section in `src/data/sections.ts`. The
vocabulary is therefore closed, and defined in `src/data/categories.ts`:

| Axis | Meaning | Examples |
| --- | --- | --- |
| Form factor | The shape of the thing | `cli`, `web-app`, `library`, `desktop-app`, `mcp-server` |
| Platform | The host application it extends | `obsidian`, `raycast`, `p5js`, `openlaszlo` |
| Domain | Subject matter | `language-learning`, `music-tools`, `machine-embroidery`, `llm-tools` |
| Audience | Who it's built for | `education`, `student-tools`, `educator-tools` |
| Lifecycle | Superseded work kept for the record | `legacy-libraries`, `historical-js` |

### Placement rule

A project carries a form-factor category (or a platform category, which serves as
the form factor for extensions), a domain category for each subject it belongs to,
and audience or lifecycle categories as needed.

**Do not invent a category for a single project.** A category earns its place by
covering at least two projects or by being the filter for a section. If nothing
fits, widen an existing category rather than adding a one-off — a category no
section looks for is invisible, and it makes the vocabulary harder to navigate for
the next project.

Prefer the narrowest category that is still true. `education` means "built for
learners", not "about computer science": a music theory app is `music-tools` plus
`education`, and belongs in the Music section rather than Computer Education.

### Enforcement

`src/lib/category-vocabulary.test.ts` runs as part of `mise run check` and fails on:

- an `os:category` value that isn't in the vocabulary;
- a section filtering on a category no project carries;
- a category in the vocabulary that no project carries;
- a section that matches no projects;
- a derived category written by hand in this file.

Adding a category means adding it to the right axis in `src/data/categories.ts`
*and* giving it a section, or it will fail the fourth check above.

### Derived categories

Parsing adds categories that must never be authored here:

- `"web-app"` → also `"webapp"`
- `"command-line"`, `"command-line-tool"` → `"cli"`
- `"*-library"`, `"*-libraries"` → also `"library"`
- `library` + `os:primaryLanguage` or `os:topics` → `"javascript-library"`,
  `"python-library"`, `"ruby-library"`, `"p5-library"`

## Complete Example

```turtle
os:my-awesome-tool a doap:Project ;
    dc:title "My Awesome Tool" ;
    dc:description "A powerful tool for developers that automates common tasks." ;
    doap:repository "https://github.com/username/my-awesome-tool" ;
    schema:url "https://my-awesome-tool.com" ;
    os:category "development-tools", "cli" ;
    schema:dateCreated "2024-01-01T00:00:00Z" ;
    schema:dateModified "2024-11-15T12:30:00Z" ;
    os:primaryLanguage "Python" ;
    os:topics "automation", "developer-tools", "cli" ;
    os:isArchived false ;
    os:includeInPortfolio true ;
    schema:thumbnail "https://images.example.com/tool-thumbnail.webp" ;
    os:exampleUsage """# Install
pip install my-awesome-tool

# Run the tool
my-tool process input.txt -o output.txt

# With options
my-tool --verbose --format json data/
""" .
```

## Hiding Projects

To hide a project from being displayed on the website, add:

```turtle
os:includeInPortfolio false ;
```

Projects without this property, or with it set to `"true"`, will be displayed.

## Data Processing

The TypeScript code in `src/data/projects.ts` parses this Turtle file and:

1. Filters projects based on `os:includeInPortfolio`
2. Normalizes categories for consistent filtering
3. Parses dates into JavaScript Date objects
4. Extracts contribution details if present
5. Returns structured project data for use in the application

## Adding New Projects

To add a new project:

1. Choose a unique identifier (e.g., `os:project-name`)
2. Add the project definition with at least the required properties
3. Assign categories from the vocabulary, following the [placement rule](#placement-rule)
4. Include any relevant optional properties
5. Run `mise run check` — the category tests will tell you if the project has
   landed somewhere no page shows
6. The project will automatically appear on the website (unless `os:includeInPortfolio` is false)

## Best Practices

1. Use meaningful, URL-safe identifiers for projects
2. Keep descriptions concise but informative
3. Use ISO 8601 format for dates
4. Include example usage for tools and libraries
5. Add thumbnails for visual projects
6. Assign categories from the vocabulary in `src/data/categories.ts`; never invent one for a single project
7. Set `os:isArchived true` for deprecated projects
8. Use `os:includeInPortfolio false` to hide work-in-progress or private projects