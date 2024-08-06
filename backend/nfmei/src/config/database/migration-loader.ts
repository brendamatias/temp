import * as fs from 'fs';
import * as path from 'path';
import { MigrationInterface } from 'typeorm';

function getPrefix(file: string): number | null {
  const base = path.basename(file);
  const m = base.match(/^(\d+)[-_]/); 
  return m ? parseInt(m[1], 10) : null;
}

function sanitizeClassName(name: string): string {
  const n = name.replace(/\.(ts|js)$/i, '').replace(/^[\d]+[-_]?/, '');
  const safe = n.replace(/[^a-zA-Z0-9_]/g, '_');
  return safe.match(/^[a-zA-Z_]/) ? safe : `M_${safe}`;
}

export function loadMigrations(isDev: boolean): (new () => MigrationInterface)[] {
  const dir = path.resolve(isDev ? 'src/migrations' : 'dist/migrations');
  const ext = isDev ? '.ts' : '.js';
  if (!fs.existsSync(dir)) return [];

  const all = fs.readdirSync(dir)
    .filter((f) => f.endsWith(ext))
    .map((f) => path.join(dir, f))
    .sort((a, b) => {
      const pa = getPrefix(a);
      const pb = getPrefix(b);
      if (pa !== null && pb !== null) return pa - pb;
      if (pa !== null) return -1;
      if (pb !== null) return 1;
      return path.basename(a).localeCompare(path.basename(b));
    });

  const BASE_TS = 1750000000000; 
  let seqFallback = 0;
  let lastTs = 0;

  return all.map((file) => {
    const mod = require(file);
    const Base = Object.values(mod).find(
      (v: any) => typeof v === 'function' && v?.prototype?.up && v?.prototype?.down
    ) as (new () => MigrationInterface) | undefined;

    if (!Base) throw new Error(`Migration inválida: ${file}`);

    const baseFile = path.basename(file);
    const baseName = sanitizeClassName(baseFile); 

    const prefix = getPrefix(file);
    let ts = prefix !== null ? (BASE_TS + prefix) : (BASE_TS + 100000 + (++seqFallback));
    if (ts <= lastTs) ts = lastTs + 1;
    lastTs = ts;

    const Wrapped = new Function(
      'Base',
      'name',
      `return class ${baseName}${ts} extends Base { name = name; }`
    )(Base, `${baseName}${ts}`) as new () => MigrationInterface;

    return Wrapped;
  });
}
