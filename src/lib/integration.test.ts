import { type ChildProcess, spawn } from "node:child_process";
import { JSDOM } from "jsdom";
import { afterAll, beforeAll, describe, expect, test } from "vitest";

// Helper function to start the server
async function startServer(port: number): Promise<ChildProcess> {
	console.log("Starting Astro development server for integration tests...");

	// Get the project root directory
	const projectRoot = process.cwd();

	// Spawn the Astro development server with the specified port
	const serverProcess = spawn("bun", ["run", "astro", "dev", "--port", port.toString()], {
		cwd: projectRoot,
		stdio: ["ignore", "pipe", "pipe"],
		env: {
			...process.env,
			// Disable browser opening automatically
			BROWSER: "none",
		},
	});

	if (serverProcess.stderr) {
		serverProcess.stderr.on("data", (data) => {
			const chunk = data.toString().trim();
			if (chunk.startsWith('error: script "astro" exited with code 143')) {
				// ignore expected message
			} else if (chunk.startsWith("$ astro dev --port")) {
				console.log(`Server message: ${chunk}`);
			} else {
				console.error(`Unexpected server stderr: ${chunk}`);
			}
		});
	}

	// Handle server process termination
	serverProcess.on("close", (code) => {
		if (code === 143) {
			console.info(`Server process exited with code ${code} (expected)`);
		} else if (code !== null && code !== 0) {
			console.error(`Server process exited with code ${code}`);
		}
	});

	// Wait for the server to be ready with faster polling
	const maxWaitTime = 20000; // 20 seconds timeout (reduced from 30)
	const startTime = Date.now();

	// Poll until the server is ready or timeout
	while (Date.now() - startTime < maxWaitTime) {
		try {
			const response = await fetch(`http://localhost:${port}`);
			if (response.ok) {
				return serverProcess;
			}
		} catch (error) {
			// Server not ready yet, wait and retry
			await new Promise((resolve) => setTimeout(resolve, 200)); // Reduced wait time
		}
	}

	throw new Error("Timeout waiting for development server to start");
}

// Helper function to stop the server
function stopServer(serverProcess: ChildProcess) {
	console.log("Shutting down Astro development server...");
	serverProcess.kill();
}

describe("Integration Tests for Page Rendering", () => {
	const serverPort = 4321; // Use a dedicated port for testing
	const BASE_URL = `http://localhost:${serverPort}`;
	let serverProcess: ChildProcess | null = null;

	// Start server once before all tests
	beforeAll(async () => {
		// Start the Astro development server
		serverProcess = await startServer(serverPort);
		// Give the server a moment to fully initialize
		await new Promise((resolve) => setTimeout(resolve, 1000));
	}, 25000); // 25 second timeout for server startup

	// Cleanup after all tests
	afterAll(() => {
		if (serverProcess) {
			stopServer(serverProcess);
		}
	});

	// Helper function to fetch HTML from page and parse with JSDOM
	async function getRenderedPage(path: string) {
		try {
			const response = await fetch(`${BASE_URL}${path}`);

			if (!response.ok) {
				throw new Error(`Failed to fetch ${path}: ${response.status} ${response.statusText}`);
			}

			const html = await response.text();
			const dom = new JSDOM(html);
			return dom.window.document;
		} catch (error) {
			throw new Error(`Error fetching ${path}: ${error instanceof Error ? error.message : String(error)}`);
		}
	}

	// Helper function to check if a page has projects
	async function checkPageHasProjects(pagePath: string, pageTitle: string) {
		try {
			const document = await getRenderedPage(pagePath);

			// Check if the page contains the title anywhere in the document
			const pageContent = document.body?.textContent || "";
			expect(pageContent.includes(pageTitle)).toBe(true);

			// Check for project titles directly in the DOM
			const projectTitles = Array.from(document.querySelectorAll(".project-title")).map((el) => el.textContent);
			expect(projectTitles.length).toBeGreaterThan(0);
			return true;
		} catch (error) {
			console.error(`Error checking page ${pagePath}:`, error);
			return false;
		}
	}

	// Individual tests for each page - this allows parallel execution
	test("Web Apps page should have projects", async () => {
		const result = await checkPageHasProjects("/software/web-apps", "Web Apps");
		expect(result).toBe(true);
	});

	test("Command Line Tools page should have projects", async () => {
		const result = await checkPageHasProjects("/software/command-line", "Command Line Tools");
		expect(result).toBe(true);
	});

	test("Libraries page should have projects", async () => {
		const result = await checkPageHasProjects("/software/libraries", "Libraries");
		expect(result).toBe(true);
	});

	test("P5.js page should have projects", async () => {
		const result = await checkPageHasProjects("/p5js", "P5.js Tools & Libraries");
		expect(result).toBe(true);
	});

	test("Embroidery topic page should have projects", async () => {
		const result = await checkPageHasProjects("/topics/embroidery", "Embroidery");
		expect(result).toBe(true);
	});

	test("P5.js topic page should have projects", async () => {
		const result = await checkPageHasProjects("/topics/p5js", "p5.js");
		expect(result).toBe(true);
	});
});
