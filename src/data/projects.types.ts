export interface Project {
	name: string;
	repo?: string; // Repository URL
	website?: string; // Project website URL
	description: string;
	categories: string[];
	primaryLanguage?: string;
	dateCreated?: string;
	dateModified?: string; // Added dateModified
	isArchived?: boolean;
}

export type ProjectCategory = string;
