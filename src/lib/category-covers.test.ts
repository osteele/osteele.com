import { describe, expect, test } from "bun:test";
import { coverMotifs, coverPalettes } from "@/lib/category-covers";

const SOFTWARE_PAGE = new URL("../pages/software.astro", import.meta.url).pathname;

/**
 * The `/software` category tiles are declared inline in `software.astro`. Parse them
 * out rather than restating them here, so a tile added there without a matching motif
 * or palette fails this test instead of silently rendering a blank cover.
 */
async function softwareTiles(): Promise<{ id: string; color: string }[]> {
	const source = await Bun.file(SOFTWARE_PAGE).text();
	const tiles = [...source.matchAll(/\bid:\s*"([^"]+)"[\s\S]*?\bcolor:\s*"([^"]+)"/g)].map((match) => ({
		id: match[1],
		color: match[2],
	}));
	if (tiles.length === 0) throw new Error(`No category tiles parsed from ${SOFTWARE_PAGE}`);
	return tiles;
}

describe("category covers", () => {
	test("every software tile has a motif", async () => {
		const tiles = await softwareTiles();
		expect(tiles.length).toBeGreaterThan(11);
		const missing = tiles.filter((tile) => typeof coverMotifs[tile.id] !== "function").map((tile) => tile.id);
		expect(missing).toEqual([]);
	});

	test("every software tile color has light and dark palettes", async () => {
		const tiles = await softwareTiles();
		const missing = tiles
			.filter((tile) => !coverPalettes[tile.color]?.light || !coverPalettes[tile.color]?.dark)
			.map((tile) => tile.color);
		expect(missing).toEqual([]);
	});
});
