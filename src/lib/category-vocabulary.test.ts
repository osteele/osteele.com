import { describe, expect, test } from "bun:test";
import { readFileSync } from "node:fs";
import { join } from "node:path";
import { DERIVED_CATEGORIES, KNOWN_CATEGORIES, isAuthoredCategory, isKnownCategory } from "@/data/categories";
import { projectsData } from "@/data/projects";
import * as sectionModule from "@/data/sections";
import type { Section } from "@/lib/sections";

const sectionArrays = Object.entries(sectionModule).filter((entry): entry is [string, Section[]] =>
	Array.isArray(entry[1]),
);

/** Every category a section filters on, including its subsections'. */
const sectionCategories = (section: Section): string[] => [
	...(section.categories ?? []),
	...(section.subsections?.flatMap((subsection) => subsection.categories ?? []) ?? []),
];

/** The `os:category` values as written, before parsing adds derived categories. */
const authoredCategoriesInTurtle = (): string[] => {
	const turtle = readFileSync(join(import.meta.dir, "../data/projects.ttl"), "utf-8");
	return [...turtle.matchAll(/os:category\s+([^;.]+)/g)].flatMap((match) =>
		[...match[1].matchAll(/"([^"]+)"/g)].map((value) => value[1]),
	);
};

describe("Category vocabulary", () => {
	test("projects.ttl only authors categories in the vocabulary", () => {
		const unknown = [...new Set(authoredCategoriesInTurtle())].filter((category) => !isAuthoredCategory(category));

		expect(unknown).toEqual([]);
	});

	test("loaded projects only carry known categories", () => {
		const unknown = new Map<string, string[]>();

		for (const project of projectsData.projects) {
			for (const category of project.categories) {
				if (isKnownCategory(category)) continue;
				unknown.set(category, [...(unknown.get(category) ?? []), project.name]);
			}
		}

		expect(Object.fromEntries(unknown)).toEqual({});
	});

	test("sections only filter on known categories", () => {
		const unknown = new Map<string, string[]>();

		for (const [arrayName, sections] of sectionArrays) {
			for (const section of sections) {
				for (const category of sectionCategories(section)) {
					if (isKnownCategory(category)) continue;
					unknown.set(category, [...(unknown.get(category) ?? []), `${arrayName}.${section.id}`]);
				}
			}
		}

		expect(Object.fromEntries(unknown)).toEqual({});
	});

	test("every category in the vocabulary is carried by a project", () => {
		const inUse = new Set(projectsData.projects.flatMap((project) => project.categories));
		const unused = KNOWN_CATEGORIES.filter((category) => !inUse.has(category));

		expect(unused).toEqual([]);
	});

	test("every section matches at least one project", () => {
		const empty: string[] = [];

		for (const [arrayName, sections] of sectionArrays) {
			for (const section of sections) {
				const categories = new Set([section.id, ...sectionCategories(section)]);
				const topics = new Set(section.topics ?? []);
				const matched = projectsData.projects.some(
					(project) =>
						project.categories.some((category) => categories.has(category)) ||
						(project.topics ?? []).some((topic) => topics.has(topic)),
				);

				if (!matched) empty.push(`${arrayName}.${section.id}`);
			}
		}

		expect(empty).toEqual([]);
	});

	test("derived categories are never authored", () => {
		const authored = new Set<string>(authoredCategoriesInTurtle());
		const leaked = DERIVED_CATEGORIES.filter((category) => authored.has(category));

		expect(leaked).toEqual([]);
	});
});
