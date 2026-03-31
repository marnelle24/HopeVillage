import { copyFileSync, existsSync } from 'node:fs';
import { resolve } from 'node:path';
import { fileURLToPath } from 'node:url';

const root = fileURLToPath(new URL('..', import.meta.url));
const src = resolve(root, 'public/build/manifest.webmanifest');
const dest = resolve(root, 'public/manifest.webmanifest');
if (existsSync(src)) {
    copyFileSync(src, dest);
}
