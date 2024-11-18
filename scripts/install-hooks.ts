import { copyFile, chmod } from "node:fs/promises";
import { join } from "node:path";

async function installHooks() {
  const hooks = ["pre-push", "pre-commit"];

  for (const hook of hooks) {
    const sourceFile = join(process.cwd(), `scripts/git-hooks/${hook}`);
    const targetFile = join(process.cwd(), `.git/hooks/${hook}`);

    await copyFile(sourceFile, targetFile);
    await chmod(targetFile, 0o755); // Make executable
    console.log(`✅ Installed ${hook} hook`);
  }
}

installHooks().catch(console.error);
