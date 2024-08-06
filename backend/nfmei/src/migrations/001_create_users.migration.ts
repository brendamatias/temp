import { MigrationInterface, QueryRunner } from 'typeorm';

export class CreateUsers implements MigrationInterface {
  name = 'CreateUsers';

  public async up(qr: QueryRunner): Promise<void> {
    const schema = process.env.DB_SCHEMA || 'public';

    await qr.query(`
      CREATE EXTENSION IF NOT EXISTS "pgcrypto";
      CREATE EXTENSION IF NOT EXISTS pg_trgm;  
      CREATE DOMAIN money_br AS NUMERIC(14, 2) CHECK (VALUE >= 0);
    `)
    await qr.query(`CREATE SCHEMA IF NOT EXISTS "${schema}"`);

    await qr.query(`
      CREATE TABLE IF NOT EXISTS "${schema}"."users" (
        "id" bigserial PRIMARY KEY,
        "email" varchar(255) NOT NULL,
        "password" varchar(255) NOT NULL,
        "role" varchar(32) NOT NULL DEFAULT 'user',
        "is_active" boolean NOT NULL DEFAULT true,
        "full_name" varchar(255),
        "phone" varchar(32),
        "created_at" timestamptz NOT NULL DEFAULT now(),
        "updated_at" timestamptz NOT NULL DEFAULT now(),
        CONSTRAINT "UQ_users_email" UNIQUE ("email"),
        CONSTRAINT "UQ_users_phone" UNIQUE ("phone")
      );
    `);

    await qr.query(`
      CREATE INDEX IF NOT EXISTS "IDX_users_created_at"
      ON "${schema}"."users" ("created_at");
    `);
  }

  public async down(qr: QueryRunner): Promise<void> {
    const schema = process.env.DB_SCHEMA || 'public';

    await qr.query(`DROP INDEX IF EXISTS "${schema}"."IDX_users_created_at";`);
    await qr.query(`DROP TABLE IF EXISTS "${schema}"."users";`);
  }
}
