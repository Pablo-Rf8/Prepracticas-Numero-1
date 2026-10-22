import { cp, mkdir } from 'node:fs/promises';

await mkdir('dist', { recursive: true });

for (const file of ['build', 'favicon.ico', 'robots.txt']) {
    await cp(`backend/public/${file}`, `dist/${file}`, { recursive: true });
}
