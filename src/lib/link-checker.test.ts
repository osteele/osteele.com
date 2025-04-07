import { type ChildProcess, spawn } from "node:child_process";
import { dirname, join } from "node:path";
import { fileURLToPath } from "node:url";
import { LinkChecker } from "linkinator";
import { beforeAll, describe, expect, test } from "vitest";

const currentDir = dirname(fileURLToPath(import.meta.url));
const projectRoot = join(currentDir, "../..");
const distDir = join(projectRoot, "dist");

async function buildSite(): Promise<void> {
	await new Promise<void>((resolve, reject) => {
		const process = spawn("bun", ["run", "astro", "build"], {
			cwd: projectRoot,
			stdio: ["ignore", "ignore", "inherit"],
		});

		process.on("close", (code) => {
			if (code === 0) {
				resolve();
			} else {
				reject(new Error(`Build failed with code ${code}`));
			}
		});

		process.on("error", reject);
	});
}

describe("Link Checker Tests", () => {
	beforeAll(async () => {
		await buildSite();
	}, 60000); // Allow time for the build

	test(
		"all internal links should be valid",
		async () => {
			const checker = new LinkChecker();
			const result = await checker.check({
				path: distDir,
				recurse: true,
				linksToSkip: [
					// Skip all external links
					"^https?://(?!localhost)",
				],
			});

			console.log(`Checked ${result.links.length} internal links`);

			const brokenLinks = result.links.filter((link) => link.state === "BROKEN");
			if (brokenLinks.length > 0) {
				console.error("\nBroken internal links found:");
				for (const link of brokenLinks) {
					console.error(`${link.url} (status: ${link.status}) on page ${link.parent}`);
				}
			}

			expect(brokenLinks).toHaveLength(0);
		},
		{ timeout: 60000 },
	);
});
