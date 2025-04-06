import { type ChildProcess, spawn } from "node:child_process";
import { JSDOM } from "jsdom";
import { describe, expect, test } from "vitest";

async function startServer(
	port = 5678, // Use a non-standard port
): Promise<{ process: ChildProcess; url: string }> {
	return new Promise((resolve, reject) => {
		const process = spawn("bun", ["run", "dev", "--port", port.toString()], {
			stdio: ["ignore", "pipe", "pipe"],
		});

		let output = "";
		let actualPort = port;

		process.stdout.on("data", (data) => {
			const chunk = data.toString();
			output += chunk;

			// Check if port was changed by the server
			const portMatch = chunk.match(/http:\/\/localhost:(\d+)/);
			if (portMatch) {
				actualPort = Number.parseInt(portMatch[1], 10);
			}

			if (output.includes("Local")) {
				resolve({ process, url: `http://localhost:${actualPort}` });
			}
		});

		process.stderr.on("data", (data) => {
			const chunk = data.toString().trim();
			const isExpectedMessage =
				chunk.startsWith("$ astro dev --port") || chunk.startsWith('error: script "astro" exited with code 143');

			if (isExpectedMessage) {
				console.log(`Server message: ${chunk}`);
			} else {
				console.error(`Unexpected server stderr: ${chunk}`);
			}
			const portMatch = chunk.match(/http:\/\/localhost:(\d+)/);
			if (portMatch) {
				actualPort = Number.parseInt(portMatch[1], 10);
			}
		});

		process.on("error", (err) => {
			reject(err);
		});

		// Timeout after 30 seconds
		setTimeout(() => {
			process.kill();
			reject(new Error("Server startup timeout"));
		}, 30000);
	});
}

async function stopServer(server: { process: ChildProcess }): Promise<void> {
	server.process.kill();
}

async function getInternalLinks(url: string): Promise<Set<string>> {
	try {
		const response = await fetch(url);
		const html = await response.text();
		const dom = new JSDOM(html);
		const links = dom.window.document.querySelectorAll("a");
		const internalLinks = new Set<string>();

		for (const link of links) {
			const href = link.getAttribute("href");
			if (
				href &&
				!href.startsWith("http") &&
				!href.startsWith("mailto:") &&
				!href.startsWith("#") &&
				!href.includes("javascript:")
			) {
				internalLinks.add(href);
			}
		}

		return internalLinks;
	} catch (error) {
		console.error(`Error getting links from ${url}:`, error);
		return new Set();
	}
}

async function isValidLink(baseUrl: string, path: string): Promise<boolean> {
	try {
		const url = new URL(path, baseUrl);
		const response = await fetch(url.toString());
		return response.ok;
	} catch (error) {
		console.error(`Error checking link ${path}:`, error);
		return false;
	}
}

describe("Internal links", () => {
	test(
		"all internal links should be valid",
		async () => {
			const port = 5678;
			const server = await startServer(port);
			const checkedLinks = new Set<string>();
			const brokenLinks = new Set<string>();
			const brokenLinkSources = new Map<string, string>();

			try {
				const pagesToCheck = new Set<string>(["/"]);
				const checkedPages = new Set<string>();

				while (pagesToCheck.size > 0) {
					const pageValue = pagesToCheck.values().next().value;
					// Skip if we somehow got an undefined value
					if (!pageValue) continue;

					const page: string = pageValue;
					pagesToCheck.delete(page);
					checkedPages.add(page);

					const links = await getInternalLinks(`${server.url}${page}`);
					for (const link of links) {
						if (!checkedLinks.has(link)) {
							checkedLinks.add(link);
							const isValid = await isValidLink(server.url, link);

							if (!isValid) {
								brokenLinks.add(link);
								brokenLinkSources.set(link, page);
								console.log(`Found broken link: ${link} on page ${page}`);
							} else if (!checkedPages.has(link) && !pagesToCheck.has(link)) {
								pagesToCheck.add(link);
							}
						}
					}
				}

				if (brokenLinks.size > 0) {
					console.error("Broken links found:");
					for (const link of brokenLinks) {
						const sourcePage = brokenLinkSources.get(link);
						if (sourcePage) {
							console.error(`${link} (found on page ${sourcePage})`);
						}
					}
				}

				expect(brokenLinks.size).toBe(0);
			} finally {
				await stopServer(server);
			}
		},
		{ timeout: 60000 },
	);
});
