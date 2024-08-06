import { MigrationInterface, QueryRunner } from 'typeorm';

export class CreateSettings implements MigrationInterface {
  name = 'CreateSettings';

  public async up(qr: QueryRunner): Promise<void> {
    const schema = process.env.DB_SCHEMA || 'public';

    await qr.query(`CREATE SCHEMA IF NOT EXISTS "${schema}"`);
    
    await qr.query(`
      CREATE TABLE IF NOT EXISTS "${schema}"."settings" (
        "id" bigserial PRIMARY KEY,
        "user_id" bigint NOT NULL,
        "mei_annual_limit" numeric,
        "monthly_reminder_day" int,
        "revenue_threshold_ratio" numeric,
        "notify_email" boolean NOT NULL DEFAULT false,
        "notify_sms" boolean NOT NULL DEFAULT false,
        "created_at" timestamptz NOT NULL DEFAULT now(),
        "updated_at" timestamptz NOT NULL DEFAULT now(),
        CONSTRAINT "UQ_settings_user" UNIQUE ("user_id"),
        CONSTRAINT "FK_settings_user"
          FOREIGN KEY ("user_id")
          REFERENCES "${schema}"."users"("id")
          ON DELETE CASCADE
          ON UPDATE NO ACTION
      );
    `);

    await qr.query(`
      CREATE INDEX IF NOT EXISTS "IDX_settings_created_at"
      ON "${schema}"."settings" ("created_at");
    `);
  }

  public async down(qr: QueryRunner): Promise<void> {
    const schema = process.env.DB_SCHEMA || 'public';

    await qr.query(`DROP INDEX IF EXISTS "${schema}"."IDX_settings_created_at";`);
    await qr.query(`DROP TABLE IF EXISTS "${schema}"."settings";`);
  }
}
