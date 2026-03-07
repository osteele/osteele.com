import sharp from "sharp";
import { existsSync, mkdirSync } from "node:fs";
import { join } from "node:path";

const WIDTH = 1200;
const HEIGHT = 630;

const outputDir = join(process.cwd(), "public/images");
if (!existsSync(outputDir)) {
	mkdirSync(outputDir, { recursive: true });
}

const svg = `
<svg width="${WIDTH}" height="${HEIGHT}" xmlns="http://www.w3.org/2000/svg">
  <defs>
    <linearGradient id="bg" x1="0%" y1="0%" x2="100%" y2="100%">
      <stop offset="0%" style="stop-color:#1e3a5f;stop-opacity:1" />
      <stop offset="100%" style="stop-color:#0f172a;stop-opacity:1" />
    </linearGradient>
  </defs>
  <rect width="${WIDTH}" height="${HEIGHT}" fill="url(#bg)" />
  <text x="100" y="260" font-family="Georgia, serif" font-size="72" font-weight="bold" fill="#ffffff">
    Oliver Steele
  </text>
  <text x="100" y="340" font-family="Arial, sans-serif" font-size="32" fill="#94a3b8">
    Researcher · Engineer · Educator · Maker
  </text>
  <text x="100" y="420" font-family="Arial, sans-serif" font-size="24" fill="#64748b">
    osteele.com
  </text>
</svg>`;

await sharp(Buffer.from(svg)).png().toFile(join(outputDir, "og-image.png"));
console.log("OG image generated at public/images/og-image.png");
