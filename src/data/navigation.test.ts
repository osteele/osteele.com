import { describe, expect, test } from "bun:test";
import { localNavigation, matchesNavigationPath } from "./navigation";

describe("local navigation", () => {
	test("presents the primary destinations in visitor-oriented order", () => {
		expect(localNavigation.map(({ label }) => label)).toEqual(["Work", "Software", "Teaching", "About"]);
		expect(localNavigation.map(({ href }) => href)).toEqual(["/work", "/software", "/teaching", "/about"]);
	});

	test("has unique destinations and marks each destination as active", () => {
		expect(new Set(localNavigation.map(({ href }) => href)).size).toBe(localNavigation.length);
		for (const item of localNavigation) {
			expect(item.activePaths).toContain(item.href);
		}
	});

	test("matches section descendants without matching similarly named routes", () => {
		const software = localNavigation.find(({ label }) => label === "Software");
		expect(software).toBeDefined();
		if (!software) return;

		expect(matchesNavigationPath("/software/command-line", software)).toBe(true);
		expect(matchesNavigationPath("/software-notes", software)).toBe(false);
	});
});
