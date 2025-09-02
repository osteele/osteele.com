// Define improved tool type system
export interface Tool {
	name: string;
	url?: string;
	desc?: string;
	previously?: boolean;
}

export interface ToolGroup {
	tools: Tool[];
	desc?: string;
}

// Helper function to create tool groups
const toolGroup = (tools: Tool[], desc?: string): ToolGroup => ({
	tools,
	desc,
});

// Define tools data structure with new schema
export const softwareEngineering = {
	"Development Tools": [
		toolGroup(
			[
				{
					name: "Visual Studio Code",
					url: "https://code.visualstudio.com/",
				},
			],
			"for general development",
		),
		toolGroup([{ name: "Claude Code", url: "https://claude.ai/code" }], "for AI-assisted coding"),
		toolGroup([{ name: "Zed", url: "https://zed.dev/" }], "as a fast text editor"),
		toolGroup([
			{ name: "Cursor", url: "https://cursor.com/", previously: true },
			{
				name: "Windsurf",
				url: "https://codeium.com/windsurf",
				previously: true,
			},
		]),
	],
	"Database Tools": [
		toolGroup([{ name: "Retcon", url: "https://retcon.app" }], "modern database client"),
		toolGroup([{ name: "Postico 2", url: "https://eggerapps.at/postico2/" }], "PostgreSQL client for Mac"),
		toolGroup([{ name: "SQLPro Studio", url: "https://www.sqlprostudio.com/" }], "multi-database management"),
		toolGroup([{ name: "TablePlus", url: "https://www.tableplus.io" }], "modern native database client"),
		toolGroup([{ name: "Base", url: "https://menial.co.uk/" }], "SQLite database editor"),
	],
	"Version Control": [
		toolGroup(
			[
				{ name: "Jujutsu (jj)", url: "https://jj-vcs.github.io/jj/" },
				{ name: "Git", url: "https://git-scm.com/" },
			],
			"for version control",
		),
		toolGroup([{ name: "Retcon", url: "https://retcon.app" }], "visual Git/Jujutsu client"),
		toolGroup([
			{
				name: "GitHub Desktop",
				url: "https://desktop.github.com/",
				previously: true,
			},
		]),
	],
	"Git Tools": [
		toolGroup(
			[
				{
					name: "diff-so-fancy",
					url: "https://github.com/so-fancy/diff-so-fancy",
				},
			],
			"better git diffs",
		),
		toolGroup(
			[
				{
					name: "interactive-rebase-tool",
					url: "https://github.com/MitMaro/git-interactive-rebase-tool",
				},
			],
			"visual git rebase",
		),
		toolGroup([{ name: "git-lfs", url: "https://git-lfs.github.com/" }], "large file storage"),
		toolGroup([{ name: "Kaleidoscope", url: "https://kaleidoscope.app/" }], "diff and merge tool"),
	],
	"Terminal & Shell": [
		toolGroup([{ name: "Warp", url: "https://www.warp.dev/" }], "AI-powered terminal"),
		toolGroup([{ name: "zsh", url: "https://www.zsh.org/" }], "with Prezto framework"),
		toolGroup([{ name: "iTerm2", url: "https://iterm2.com/", previously: true }]),
	],
	"Shell Tools": [
		toolGroup([{ name: "atuin", url: "https://atuin.sh/" }], "shell history sync & search"),
		toolGroup([{ name: "zoxide", url: "https://github.com/ajeetdsouza/zoxide" }], "smart directory jumping"),
		toolGroup([{ name: "fzf", url: "https://github.com/junegunn/fzf" }], "fuzzy finder"),
		toolGroup([{ name: "direnv", url: "https://direnv.net/" }], "environment switcher"),
		toolGroup([{ name: "eza", url: "https://eza.rocks/" }], "modern ls replacement"),
	],
	"Tech Stack": {
		type: "text",
		content: "For my development tech stack including languages, frameworks, and deployment platforms, see my",
		link: { text: "About page", url: "/about" },
	},
};

export const researchLearning = {
	"Academic Workflow": [
		toolGroup([{ name: "Obsidian", url: "https://obsidian.md/" }], "Knowledge base and note-taking"),
		toolGroup([{ name: "Zotero", url: "https://www.zotero.org/" }], "Reference management"),
		toolGroup([{ name: "RemNote", url: "https://www.remnote.com/" }], "Spaced repetition notes"),
		toolGroup([
			{ name: "Notion", url: "https://www.notion.so/", previously: true },
			{
				name: "Roam Research",
				url: "https://roamresearch.com/",
				previously: true,
			},
		]),
	],
	"Language Learning": [
		toolGroup([{ name: "Pleco", url: "https://www.pleco.com/" }], "Chinese dictionary app"),
		toolGroup([{ name: "Skritter", url: "https://skritter.com/" }], "Character writing practice"),
		toolGroup([{ name: "Duolingo", url: "https://www.duolingo.com/" }], "Gamified language learning"),
		toolGroup([{ name: "Du Chinese", url: "https://www.duchinese.net/" }], "Graded Chinese reading"),
	],
};

export const designMedia = {
	"Image Editing": [
		toolGroup(
			[
				{
					name: "Pixelmator Pro",
					url: "https://www.pixelmator.com/pro/",
				},
			],
			"Image editing",
		),
		toolGroup(
			[
				{
					name: "Affinity Photo",
					url: "https://affinity.serif.com/photo/",
				},
			],
			"Photo editing",
		),
		toolGroup(
			[
				{
					name: "Lightroom",
					url: "https://www.adobe.com/products/photoshop-lightroom.html",
				},
			],
			"Photo management",
		),
	],
	"Vector & Graphics": [
		toolGroup(
			[
				{
					name: "Affinity Designer",
					url: "https://affinity.serif.com/designer/",
				},
			],
			"Vector graphics",
		),
		toolGroup([
			{
				name: "OmniGraffle",
				url: "https://www.omnigroup.com/omnigraffle",
				previously: true,
			},
			{
				name: "Sketch",
				url: "https://www.sketch.com/",
				previously: true,
			},
		]),
	],
	"Video Production": [
		toolGroup(
			[
				{
					name: "Final Cut Pro",
					url: "https://www.apple.com/final-cut-pro/",
				},
			],
			"Video editing",
		),
		toolGroup(
			[
				{
					name: "DaVinci Resolve",
					url: "https://www.blackmagicdesign.com/products/davinciresolve",
				},
			],
			"Video editing",
		),
	],
};

export const computingDevices = {
	Computing: [
		toolGroup([{ name: 'MacBook Pro M1 13"' }], "2020 model for portable computing"),
		toolGroup([{ name: "iPad Air" }], "4th generation for note-taking and PDF markup"),
	],
	"Input Devices": [
		toolGroup(
			[
				{
					name: "MX Master 3S",
					url: "https://www.logitech.com/en-us/products/mice/mx-master-3s.html",
				},
			],
			"ergonomic mouse",
		),
		toolGroup(
			[
				{
					name: "MX Mechanical",
					url: "https://www.logitech.com/en-us/products/keyboards/mx-mechanical.html",
				},
			],
			"for home office",
		),
		toolGroup(
			[
				{
					name: "MX Mechanical Mini",
					url: "https://www.logitech.com/en-us/products/keyboards/mx-mechanical-mini.html",
				},
			],
			"for travel",
		),
	],
};

export const macosWorkflow = {
	"Quick Access": [
		toolGroup([{ name: "Raycast", url: "https://www.raycast.com/" }], "Extensible launcher"),
		toolGroup([{ name: "rcmd", url: "https://lowtechguys.com/rcmd" }], "App switching with right cmd"),
		toolGroup([{ name: "Bartender", url: "https://www.macbartender.com/" }], "Menu bar management"),
	],
	"Screen Capture": [
		toolGroup([{ name: "CleanShot X", url: "https://cleanshot.com/" }], "Screenshots & recording"),
		toolGroup([{ name: "ScreenFloat", url: "https://www.screenfloat.app/" }], "Floating screenshots"),
		toolGroup([{ name: "TextSniper", url: "https://textsniper.app/" }], "OCR text extraction"),
	],
	Automation: [
		toolGroup(
			[
				{
					name: "Keyboard Maestro",
					url: "https://www.keyboardmaestro.com/",
				},
			],
			"macOS automation",
		),
		toolGroup([{ name: "Yoink", url: "https://yoink.app/" }], "Drag and drop shelf"),
		toolGroup([{ name: "Paste", url: "https://pasteapp.io/" }], "Clipboard manager"),
	],
	Subscription: [toolGroup([{ name: "SetApp", url: "https://setapp.com/" }], "Access to 240+ apps")],
};
