export interface Contribution {
	description: string;
	pullRequest?: string;
	features?: string[];
}

export interface Project {
	name: string;
	repo?: string; // Repository URL
	website?: string; // Project website URL
	description: string;
	categories: string[];
	topics?: string[];
	primaryLanguage?: string;
	dateCreated?: Date;
	dateModified?: Date;
	isArchived?: boolean;
	exampleUsage?: string;
	thumbnail?: string;
	contribution?: Contribution; // Details if this is a contributed project
}

export type ProjectCategory = string;

/**
 * Normalize a category or subsection name for consistent matching
 * Converts a display name to a slug format for use in lookups
 */
export function normalizeCategory(name: string): string {
	return name
		.toLowerCase()
		.replace(/[^a-z0-9]+/g, "-")
		.replace(/(^-|-$)/g, "");
}
