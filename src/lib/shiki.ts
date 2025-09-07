import { type Highlighter, createHighlighter } from "shiki";

let highlighterInstance: Highlighter | null = null;

export async function getHighlighter(): Promise<Highlighter> {
	if (!highlighterInstance) {
		highlighterInstance = await createHighlighter({
			themes: ["nord"],
			langs: ["bash"],
		});
	}
	return highlighterInstance;
}

export async function highlightCode(code: string, lang = "bash", theme = "nord"): Promise<string> {
	const highlighter = await getHighlighter();
	// @ts-ignore - Shiki types may vary between versions
	return highlighter.codeToHtml(code, { lang, theme });
}
