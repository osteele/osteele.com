import { copyFile, chmod } from "node:fs/promises";
import { join } from "node:path";

async function installHooks() {
  const sourceFile = join(process.cwd(), "scripts/git-hooks/pre-push");
  const targetFile = join(process.cwd(), ".git/hooks/pre-push");

  await copyFile(sourceFile, targetFile);
  await chmod(targetFile, 0o755); // Make executable

  console.log("✅ Git hooks installed successfully");
}

installHooks().catch(console.error);
