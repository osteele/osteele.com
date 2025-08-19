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

- **`os:category`** - Categories for organizing projects (comma-separated)
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

## Category Normalization

The system automatically normalizes certain category names for consistency:

- `"web-app"`, `"web-apps"` → `"webapp"`
- `"command-line"`, `"command-line-tool"` → `"cli"`
- `"*-library"`, `"*-libraries"` → `"library"` (plus language-specific variants)

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
3. Include any relevant optional properties
4. The project will automatically appear on the website (unless `os:includeInPortfolio` is false)

## Best Practices

1. Use meaningful, URL-safe identifiers for projects
2. Keep descriptions concise but informative
3. Use ISO 8601 format for dates
4. Include example usage for tools and libraries
5. Add thumbnails for visual projects
6. Use consistent category names (they will be normalized)
7. Set `os:isArchived true` for deprecated projects
8. Use `os:includeInPortfolio false` to hide work-in-progress or private projects