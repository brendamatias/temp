import 'dotenv/config';
import { DataSource } from 'typeorm';
import { SnakeNamingStrategy } from './snake-naming.strategy';
import { loadMigrations } from './migration-loader';

const isDev = process.env.NODE_ENV === 'development';
const ssl = process.env.DB_SSL === 'true';
const migrations = loadMigrations(isDev);

console.log('[MIGRATIONS]', migrations.map(m => m.name));

const common = {
  type: 'postgres' as const,
  synchronize: false,
  logging: isDev ? ['error', 'schema'] as any : ['error'] as any,
  namingStrategy: new SnakeNamingStrategy(),
  ssl: ssl ? { rejectUnauthorized: false } : false,
  entities: [isDev ? 'src/**/*.entity.ts' : 'dist/**/*.entity.js'],
  migrations,
};

const url = process.env.DATABASE_URL;
export default new DataSource(
  url
    ? { ...common, url }
    : {
        ...common,
        host: process.env.DB_HOST || 'localhost',
        port: +(process.env.DB_PORT || 5432),
        username: process.env.DB_USER || 'postgres',
        password: process.env.DB_PASS || 'postgres',
        database: process.env.DB_NAME || 'appdb',
      },
);
