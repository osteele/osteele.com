import { readFileSync } from "node:fs";
import { join } from "node:path";
import { Parser, Store } from "n3";

export interface Project {
	name: string;
	repo?: string;
	website?: string;
	description: string;
	categories: string[];
	primaryLanguage?: string;
	dateCreated?: Date;
	dateModified?: Date;
	isArchived?: boolean;
}

export interface ProjectsData {
	projects: Project[];
}

// Category normalization map
const CATEGORY_NORMALIZATIONS: Record<string, string> = {
	"web-app": "webapp",
	"web-apps": "webapp",
	"command-line": "cli",
	"command-line-tool": "cli",
	"javascript-library": "library",
	"ruby-library": "library",
	"python-library": "library",
	"p5js-library": "library",
	"llm-library": "library",
	"javascript-libraries": "library",
	"ruby-libraries": "library",
	"python-libraries": "library",
	"p5js-libraries": "library",
	"llm-libraries": "library",
	// Add other normalizations as needed
};

// Normalize categories to ensure consistent filtering
export function normalizeCategories(categories: string[]): string[] {
	const normalizedSet = new Set<string>();

	categories.forEach((category) => {
		// Add the original category
		normalizedSet.add(category);

		// Add the normalized version if it exists
		if (CATEGORY_NORMALIZATIONS[category]) {
			normalizedSet.add(CATEGORY_NORMALIZATIONS[category]);
		}

		// Add "library" category for any library-related category
		if (category.includes("library") || category.includes("libraries")) {
			normalizedSet.add("library");
		}
	});

	return Array.from(normalizedSet);
}

// Helper function to extract literal value from RDF term
const getLiteralValue = (store: Store, subject: string, predicate: string): string | undefined => {
	const terms = store.getObjects(subject, predicate, null);
	return terms.length > 0 ? terms[0].value : undefined;
};

// Helper function to get all values for a predicate
const getAllValues = (store: Store, subject: string, predicate: string): string[] => {
	return store.getObjects(subject, predicate, null).map((term) => term.value);
};

// Export the function with the same signature as in projects.ts
export async function loadProjectsFromTurtle(): Promise<ProjectsData> {
	const ttlPath = join(process.cwd(), "src/data/projects.ttl");
	const ttlContent = readFileSync(ttlPath, "utf-8");

	const store = new Store();
	const parser = new Parser();

	// Define the namespaces
	const DC = "http://purl.org/dc/terms/";
	const DOAP = "http://usefulinc.com/ns/doap#";
	const SCHEMA = "http://schema.org/";
	const OS = "http://osteele.com/ns/";

	await new Promise<void>((resolve, reject) => {
		parser.parse(ttlContent, (error, quad) => {
			if (error) reject(error);
			if (quad) store.add(quad);
			else resolve();
		});
	});

	const subjects = store.getSubjects(null, null, null);
	const projects = subjects
		.filter((subject) => {
			const includeInPortfolio = getLiteralValue(store, subject.value, `${OS}includeInPortfolio`);
			return includeInPortfolio === undefined || includeInPortfolio === "true";
		})
		.map((subject) => {
			const subjectStr = subject.value;
			const name = getLiteralValue(store, subjectStr, `${DC}title`) || "";
			const repo = getLiteralValue(store, subjectStr, `${DOAP}repository`);
			const website = getLiteralValue(store, subjectStr, `${SCHEMA}url`);
			const description = getLiteralValue(store, subjectStr, `${DC}description`) || "";
			const rawCategories = getAllValues(store, subjectStr, `${OS}category`);
			const categories = normalizeCategories(rawCategories);
			const primaryLanguage = getLiteralValue(store, subjectStr, `${OS}primaryLanguage`);
			const dateCreatedStr = getLiteralValue(store, subjectStr, `${SCHEMA}dateCreated`);
			const dateModifiedStr = getLiteralValue(store, subjectStr, `${SCHEMA}dateModified`);
			const isArchived = getLiteralValue(store, subjectStr, `${OS}isArchived`) === "true";

			// Parse dates, return undefined if invalid
			const parseDate = (dateStr: string | undefined): Date | undefined => {
				if (!dateStr) return undefined;
				const date = new Date(dateStr);
				return Number.isNaN(date.getTime()) ? undefined : date;
			};

			const dateCreated = parseDate(dateCreatedStr);
			const dateModified = parseDate(dateModifiedStr);

			return {
				name,
				repo,
				website,
				description,
				categories,
				primaryLanguage,
				dateCreated,
				dateModified,
				isArchived,
			};
		});

	return { projects };
}

// Add the getStaticPaths function for compatibility
export const getStaticPaths = async () => {
	const { projects } = await loadProjectsFromTurtle();
	return projects;
};

// For convenience, also export the pre-loaded data
export const projectsData = await loadProjectsFromTurtle();

// Default export for compatibility with existing code
export default getStaticPaths;
