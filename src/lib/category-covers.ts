export interface CoverPalette {
	primary: string;
	secondary: string;
	faint: string;
}

export interface CoverDrawState {
	cursorVisible: boolean;
}

export type CoverMotif = (
	ctx: CanvasRenderingContext2D,
	w: number,
	h: number,
	palette: CoverPalette,
	state?: CoverDrawState,
) => void;

export type CoverTheme = "light" | "dark";

interface ThemePalettes {
	light: CoverPalette;
	dark: CoverPalette;
}

export const coverPalettes: Record<string, ThemePalettes> = {
	blue: {
		light: { primary: "#3b82f6a6", secondary: "#2563eb80", faint: "#bfdbfe70" },
		dark: { primary: "#60a5faa6", secondary: "#93c5fd80", faint: "#1e40af70" },
	},
	cyan: {
		light: { primary: "#06b6d4a6", secondary: "#0891b280", faint: "#a5f3fc70" },
		dark: { primary: "#22d3eea6", secondary: "#67e8f980", faint: "#155e7570" },
	},
	green: {
		light: { primary: "#22c55ea6", secondary: "#16a34a80", faint: "#bbf7d070" },
		dark: { primary: "#4ade80a6", secondary: "#86efac80", faint: "#16653470" },
	},
	indigo: {
		light: { primary: "#6366f1a6", secondary: "#4f46e580", faint: "#c7d2fe70" },
		dark: { primary: "#818cf8a6", secondary: "#a5b4fc80", faint: "#3730a370" },
	},
	orange: {
		light: { primary: "#f97316a6", secondary: "#ea580c80", faint: "#fed7aa70" },
		dark: { primary: "#fb923ca6", secondary: "#fdba7480", faint: "#9a341270" },
	},
	violet: {
		light: { primary: "#8b5cf6a6", secondary: "#7c3aed80", faint: "#ddd6fe70" },
		dark: { primary: "#a78bfaa6", secondary: "#c4b5fd80", faint: "#5b21b670" },
	},
	purple: {
		light: { primary: "#a855f7a6", secondary: "#9333ea80", faint: "#e9d5ff70" },
		dark: { primary: "#c084fca6", secondary: "#d8b4fe80", faint: "#6b21a870" },
	},
	sky: {
		light: { primary: "#0ea5e9a6", secondary: "#0284c780", faint: "#bae6fd70" },
		dark: { primary: "#38bdf8a6", secondary: "#7dd3fc80", faint: "#07598570" },
	},
	pink: {
		light: { primary: "#ec4899a6", secondary: "#db277780", faint: "#fbcfe870" },
		dark: { primary: "#f472b6a6", secondary: "#f9a8d480", faint: "#9d174d70" },
	},
};

export function getCoverPalette(color: string, theme: CoverTheme): CoverPalette | undefined {
	return coverPalettes[color]?.[theme];
}

function withDrawingState(ctx: CanvasRenderingContext2D, draw: () => void): void {
	ctx.save();
	draw();
	ctx.restore();
}

function drawNode(ctx: CanvasRenderingContext2D, x: number, y: number, radius: number, color: string): void {
	ctx.beginPath();
	ctx.arc(x, y, radius, 0, Math.PI * 2);
	ctx.fillStyle = color;
	ctx.fill();
}

function drawLine(
	ctx: CanvasRenderingContext2D,
	points: ReadonlyArray<readonly [number, number]>,
	color: string,
	width = 1.5,
): void {
	const first = points[0];
	if (!first) return;
	ctx.beginPath();
	ctx.moveTo(first[0], first[1]);
	for (const [x, y] of points.slice(1)) ctx.lineTo(x, y);
	ctx.strokeStyle = color;
	ctx.lineWidth = width;
	ctx.stroke();
}

function browserMotif(ctx: CanvasRenderingContext2D, w: number, h: number, palette: CoverPalette): void {
	withDrawingState(ctx, () => {
		const x = w * 0.08;
		const y = h * 0.09;
		const width = w * 0.84;
		const height = h * 0.82;
		ctx.strokeStyle = palette.primary;
		ctx.lineWidth = 1.5;
		ctx.beginPath();
		ctx.roundRect(x, y, width, height, 8);
		ctx.stroke();
		drawLine(
			ctx,
			[
				[x, y + 18],
				[x + width, y + 18],
			],
			palette.secondary,
		);
		for (let index = 0; index < 3; index++) drawNode(ctx, x + 12 + index * 9, y + 9, 2.2, palette.primary);

		ctx.fillStyle = palette.faint;
		ctx.beginPath();
		ctx.roundRect(x + 11, y + 29, width * 0.34, height - 39, 5);
		ctx.fill();
		const blocks = [
			[0.42, 0.55],
			[0.42, 0.36],
			[0.42, 0.48],
		] as const;
		blocks.forEach(([offset, length], index) => {
			ctx.beginPath();
			ctx.roundRect(x + width * offset, y + 30 + index * 17, width * length, 8, 4);
			ctx.fillStyle = index === 0 ? palette.primary : palette.faint;
			ctx.fill();
		});
	});
}

function desktopMotif(ctx: CanvasRenderingContext2D, w: number, h: number, palette: CoverPalette): void {
	withDrawingState(ctx, () => {
		const windows = [
			{ x: 0.08, y: 0.25, width: 0.48, height: 0.6 },
			{ x: 0.29, y: 0.11, width: 0.5, height: 0.62 },
			{ x: 0.48, y: 0.3, width: 0.44, height: 0.58 },
		];
		windows.forEach((windowFrame, windowIndex) => {
			const x = w * windowFrame.x;
			const y = h * windowFrame.y;
			const width = w * windowFrame.width;
			const height = h * windowFrame.height;
			ctx.fillStyle = palette.faint;
			ctx.strokeStyle = windowIndex === 1 ? palette.primary : palette.secondary;
			ctx.lineWidth = windowIndex === 1 ? 2 : 1.25;
			ctx.beginPath();
			ctx.roundRect(x, y, width, height, 6);
			ctx.fill();
			ctx.stroke();
			drawLine(
				ctx,
				[
					[x, y + 14],
					[x + width, y + 14],
				],
				palette.secondary,
			);
			for (let dot = 0; dot < 3; dot++) drawNode(ctx, x + 9 + dot * 7, y + 7, 1.7, palette.primary);
		});
	});
}

function commandLineMotif(
	ctx: CanvasRenderingContext2D,
	w: number,
	h: number,
	palette: CoverPalette,
	state: CoverDrawState = { cursorVisible: true },
): void {
	withDrawingState(ctx, () => {
		ctx.lineCap = "round";
		const left = w * 0.12;
		const rows = [h * 0.28, h * 0.5, h * 0.72];
		const lengths = [w * 0.48, w * 0.63, w * 0.35];
		ctx.font = `600 ${Math.max(13, h * 0.16)}px ui-monospace, monospace`;
		ctx.textBaseline = "middle";
		ctx.fillStyle = palette.primary;
		rows.forEach((y, index) => {
			ctx.fillText(index === 0 ? "❯" : "·", left, y);
			ctx.setLineDash([7, 5]);
			drawLine(
				ctx,
				[
					[left + 22, y],
					[left + 22 + lengths[index], y],
				],
				index === 1 ? palette.secondary : palette.primary,
				2.2,
			);
		});
		ctx.setLineDash([]);
		if (state.cursorVisible) {
			ctx.fillStyle = palette.secondary;
			ctx.fillRect(left + 27 + lengths[2], rows[2] - 7, 8, 14);
		}
	});
}

function librariesMotif(ctx: CanvasRenderingContext2D, w: number, h: number, palette: CoverPalette): void {
	withDrawingState(ctx, () => {
		const courses = [
			{ y: 0.19, offset: 0.08, widths: [0.24, 0.35, 0.2] },
			{ y: 0.43, offset: 0.16, widths: [0.32, 0.22, 0.24] },
			{ y: 0.67, offset: 0.07, widths: [0.2, 0.28, 0.34] },
		];
		courses.forEach((course, row) => {
			let x = w * course.offset;
			course.widths.forEach((fraction, column) => {
				const width = w * fraction;
				ctx.beginPath();
				ctx.roundRect(x, h * course.y, width, h * 0.18, 4);
				ctx.fillStyle = (row + column) % 3 === 0 ? palette.primary : palette.faint;
				ctx.fill();
				ctx.strokeStyle = palette.secondary;
				ctx.lineWidth = 1;
				ctx.stroke();
				x += width + w * 0.025;
			});
		});
	});
}

function developmentToolsMotif(ctx: CanvasRenderingContext2D, w: number, h: number, palette: CoverPalette): void {
	withDrawingState(ctx, () => {
		const xs = [0.1, 0.27, 0.44, 0.61, 0.78, 0.91].map((value) => value * w);
		const lanes = [0.29, 0.52, 0.75].map((value) => value * h);
		ctx.lineCap = "round";
		drawLine(
			ctx,
			xs.map((x) => [x, lanes[1]]),
			palette.secondary,
			2,
		);
		drawLine(
			ctx,
			[
				[xs[1], lanes[1]],
				[xs[2], lanes[0]],
				[xs[3], lanes[0]],
				[xs[4], lanes[1]],
			],
			palette.primary,
			2.2,
		);
		drawLine(
			ctx,
			[
				[xs[2], lanes[1]],
				[xs[3], lanes[2]],
				[xs[4], lanes[2]],
				[xs[5], lanes[1]],
			],
			palette.faint,
			2,
		);
		for (let index = 0; index < xs.length; index++) drawNode(ctx, xs[index], lanes[1], 4, palette.secondary);
		drawNode(ctx, xs[2], lanes[0], 4.5, palette.primary);
		drawNode(ctx, xs[3], lanes[0], 4.5, palette.primary);
		drawNode(ctx, xs[3], lanes[2], 4, palette.faint);
		drawNode(ctx, xs[4], lanes[2], 4, palette.faint);
	});
}

function drawArrow(
	ctx: CanvasRenderingContext2D,
	from: readonly [number, number],
	to: readonly [number, number],
	color: string,
	emphasized = false,
): void {
	const angle = Math.atan2(to[1] - from[1], to[0] - from[0]);
	const endX = to[0] - Math.cos(angle) * 8;
	const endY = to[1] - Math.sin(angle) * 8;
	drawLine(ctx, [from, [endX, endY]], color, emphasized ? 2.4 : 1.3);
	ctx.beginPath();
	ctx.moveTo(endX, endY);
	ctx.lineTo(endX - Math.cos(angle - 0.55) * 7, endY - Math.sin(angle - 0.55) * 7);
	ctx.lineTo(endX - Math.cos(angle + 0.55) * 7, endY - Math.sin(angle + 0.55) * 7);
	ctx.closePath();
	ctx.fillStyle = color;
	ctx.fill();
}

function agentToolsMotif(ctx: CanvasRenderingContext2D, w: number, h: number, palette: CoverPalette): void {
	withDrawingState(ctx, () => {
		const nodes = [
			[0.12, 0.5],
			[0.31, 0.23],
			[0.49, 0.61],
			[0.66, 0.28],
			[0.87, 0.54],
			[0.7, 0.78],
		].map(([x, y]) => [x * w, y * h] as const);
		const edges = [
			[0, 1, true],
			[0, 2, false],
			[1, 3, false],
			[2, 3, true],
			[2, 5, false],
			[3, 4, true],
			[5, 4, false],
		] as const;
		for (const [from, to, emphasized] of edges) {
			drawArrow(ctx, nodes[from], nodes[to], emphasized ? palette.primary : palette.faint, emphasized);
		}
		nodes.forEach(([x, y], index) => drawNode(ctx, x, y, index === 2 || index === 3 ? 6 : 4.5, palette.secondary));
	});
}

function academicResearchMotif(ctx: CanvasRenderingContext2D, w: number, h: number, palette: CoverPalette): void {
	withDrawingState(ctx, () => {
		const center = [w * 0.47, h * 0.49] as const;
		const nodes = [
			[0.11, 0.22],
			[0.16, 0.73],
			[0.42, 0.14],
			[0.72, 0.2],
			[0.86, 0.55],
			[0.67, 0.81],
		].map(([x, y]) => [x * w, y * h] as const);
		nodes.forEach((node, index) => {
			drawLine(ctx, [center, node], index % 2 === 0 ? palette.primary : palette.faint, index < 3 ? 1.8 : 1.2);
		});
		drawLine(ctx, [nodes[0], nodes[2]], palette.faint);
		drawLine(ctx, [nodes[3], nodes[4]], palette.secondary);
		drawLine(ctx, [nodes[4], nodes[5]], palette.faint);
		nodes.forEach(([x, y], index) => drawNode(ctx, x, y, index % 2 === 0 ? 4 : 3, palette.secondary));
		ctx.fillStyle = palette.primary;
		ctx.strokeStyle = palette.secondary;
		ctx.lineWidth = 1.4;
		ctx.beginPath();
		ctx.roundRect(center[0] - 12, center[1] - 16, 24, 32, 3);
		ctx.fill();
		ctx.stroke();
	});
}

function p5jsMotif(ctx: CanvasRenderingContext2D, w: number, h: number, palette: CoverPalette): void {
	withDrawingState(ctx, () => {
		const goldenAngle = Math.PI * (3 - Math.sqrt(5));
		const count = 115;
		const centerX = w * 0.5;
		const centerY = h * 0.5;
		const maxRadius = Math.min(w * 0.43, h * 0.45);
		for (let index = 0; index < count; index++) {
			const progress = index / (count - 1);
			const radius = Math.sqrt(progress) * maxRadius;
			const angle = index * goldenAngle;
			const x = centerX + Math.cos(angle) * radius * (w / Math.max(w, h * 2.5));
			const y = centerY + Math.sin(angle) * radius;
			drawNode(ctx, x, y, 1.1 + progress * 2.1, index % 4 === 0 ? palette.secondary : palette.primary);
		}
	});
}

function obsidianMotif(ctx: CanvasRenderingContext2D, w: number, h: number, palette: CoverPalette): void {
	withDrawingState(ctx, () => {
		const center = [w * 0.5, h * 0.5] as const;
		const radii = [0.34, 0.27, 0.36, 0.3, 0.38, 0.29, 0.35];
		const nodes = radii.map((radius, index) => {
			const angle = -Math.PI / 2 + (index / radii.length) * Math.PI * 2;
			return [center[0] + Math.cos(angle) * w * radius, center[1] + Math.sin(angle) * h * radius] as const;
		});
		nodes.forEach((node, index) => drawLine(ctx, [center, node], index % 3 === 0 ? palette.primary : palette.faint));
		drawLine(ctx, [nodes[0], nodes[2]], palette.secondary);
		drawLine(ctx, [nodes[3], nodes[5]], palette.faint);
		drawLine(ctx, [nodes[5], nodes[6]], palette.secondary);
		nodes.forEach(([x, y], index) => drawNode(ctx, x, y, index % 3 === 0 ? 4.5 : 3.2, palette.secondary));
		drawNode(ctx, center[0], center[1], 8, palette.primary);
	});
}

function languageLearningMotif(ctx: CanvasRenderingContext2D, w: number, h: number, palette: CoverPalette): void {
	withDrawingState(ctx, () => {
		const top = [0.12, 0.27, 0.43, 0.63, 0.78];
		const bottom = [0.09, 0.25, 0.47, 0.64, 0.82];
		const widths = [0.1, 0.12, 0.15, 0.09, 0.11];
		ctx.lineCap = "round";
		top.forEach((x, index) => {
			drawLine(
				ctx,
				[
					[w * x, h * 0.28],
					[w * (x + widths[index]), h * 0.28],
				],
				palette.primary,
				5,
			);
		});
		bottom.forEach((x, index) => {
			drawLine(
				ctx,
				[
					[w * x, h * 0.73],
					[w * (x + widths[(index + 2) % widths.length]), h * 0.73],
				],
				palette.secondary,
				5,
			);
		});
		const alignments = [
			[0, 0],
			[1, 1],
			[2, 3],
			[3, 2],
			[4, 4],
		] as const;
		ctx.lineWidth = 1.2;
		ctx.strokeStyle = palette.faint;
		for (const [from, to] of alignments) {
			const fromX = w * (top[from] + widths[from] / 2);
			const toX = w * (bottom[to] + widths[(to + 2) % widths.length] / 2);
			ctx.beginPath();
			ctx.moveTo(fromX, h * 0.34);
			ctx.bezierCurveTo(fromX, h * 0.52, toX, h * 0.49, toX, h * 0.67);
			ctx.stroke();
		}
	});
}

function physicalComputingMotif(ctx: CanvasRenderingContext2D, w: number, h: number, palette: CoverPalette): void {
	withDrawingState(ctx, () => {
		const columns = 13;
		const rows = 5;
		const left = w * 0.1;
		const top = h * 0.18;
		const xStep = (w * 0.8) / (columns - 1);
		const yStep = (h * 0.64) / (rows - 1);
		for (let row = 0; row < rows; row++) {
			for (let column = 0; column < columns; column++) {
				drawNode(ctx, left + column * xStep, top + row * yStep, 1.5, palette.faint);
			}
		}
		const traces = [
			[
				[1, 1],
				[4, 1],
				[4, 3],
				[7, 3],
			],
			[
				[6, 0],
				[9, 0],
				[9, 2],
				[11, 2],
			],
			[
				[2, 4],
				[2, 2],
				[6, 2],
				[6, 4],
				[10, 4],
			],
		] as const;
		traces.forEach((trace, index) => {
			const points = trace.map(([column, row]) => [left + column * xStep, top + row * yStep] as const);
			drawLine(ctx, points, index === 1 ? palette.secondary : palette.primary, 2.3);
			const first = points[0];
			const last = points.at(-1);
			if (first) drawNode(ctx, first[0], first[1], 4, palette.primary);
			if (last) drawNode(ctx, last[0], last[1], 4, palette.secondary);
		});
	});
}

function cubicPoint(t: number, start: number, control1: number, control2: number, end: number): number {
	const inverse = 1 - t;
	return inverse ** 3 * start + 3 * inverse ** 2 * t * control1 + 3 * inverse * t ** 2 * control2 + t ** 3 * end;
}

function cubicDerivative(t: number, start: number, control1: number, control2: number, end: number): number {
	const inverse = 1 - t;
	return (
		3 * inverse ** 2 * (control1 - start) + 6 * inverse * t * (control2 - control1) + 3 * t ** 2 * (end - control2)
	);
}

function machineEmbroideryMotif(ctx: CanvasRenderingContext2D, w: number, h: number, palette: CoverPalette): void {
	withDrawingState(ctx, () => {
		const x = [w * 0.08, w * 0.32, w * 0.68, w * 0.92] as const;
		const y = [h * 0.66, h * 0.08, h * 0.92, h * 0.35] as const;
		ctx.lineCap = "round";
		for (let index = 0; index <= 58; index++) {
			const t = index / 58;
			const pointX = cubicPoint(t, x[0], x[1], x[2], x[3]);
			const pointY = cubicPoint(t, y[0], y[1], y[2], y[3]);
			const dx = cubicDerivative(t, x[0], x[1], x[2], x[3]);
			const dy = cubicDerivative(t, y[0], y[1], y[2], y[3]);
			const length = Math.hypot(dx, dy) || 1;
			const normalX = -dy / length;
			const normalY = dx / length;
			const halfStitch = 5 + Math.sin(t * Math.PI) * 4;
			drawLine(
				ctx,
				[
					[pointX - normalX * halfStitch, pointY - normalY * halfStitch],
					[pointX + normalX * halfStitch, pointY + normalY * halfStitch],
				],
				index % 4 === 0 ? palette.secondary : palette.primary,
				1.25,
			);
		}
		ctx.setLineDash([3, 5]);
		drawLine(
			ctx,
			[
				[w * 0.1, h * 0.87],
				[w * 0.33, h * 0.78],
			],
			palette.secondary,
			1.4,
		);
		drawLine(
			ctx,
			[
				[w * 0.72, h * 0.17],
				[w * 0.91, h * 0.12],
			],
			palette.faint,
			1.4,
		);
	});
}

export const coverMotifs: Record<string, CoverMotif> = {
	"web-apps": browserMotif,
	"desktop-apps": desktopMotif,
	"command-line": commandLineMotif,
	libraries: librariesMotif,
	"development-tools": developmentToolsMotif,
	"agent-tools": agentToolsMotif,
	"academic-research-tools": academicResearchMotif,
	p5js: p5jsMotif,
	obsidian: obsidianMotif,
	"language-learning": languageLearningMotif,
	"physical-computing": physicalComputingMotif,
	"machine-embroidery": machineEmbroideryMotif,
};

export function getCoverMotif(id: string): CoverMotif | undefined {
	return coverMotifs[id];
}
