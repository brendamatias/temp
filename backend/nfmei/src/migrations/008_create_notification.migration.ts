import { MigrationInterface, QueryRunner } from 'typeorm';

export class CreateNotifications implements MigrationInterface {
  name = 'CreateNotifications';

  public async up(queryRunner: QueryRunner): Promise<void> {
    const schema = process.env.DB_SCHEMA || 'public';

    await queryRunner.query(`
      CREATE TABLE IF NOT EXISTS "${schema}"."notifications" (
        "id" uuid PRIMARY KEY DEFAULT gen_random_uuid(),
        "type" varchar(64) NOT NULL,
        "channel" varchar(16) NOT NULL CHECK ("channel" IN ('SMS', 'EMAIL')),
        "payload" jsonb NOT NULL DEFAULT '{}'::jsonb,
        "sent_at" timestamptz NOT NULL DEFAULT now()
      );
    `);

    await queryRunner.query(`
      CREATE INDEX IF NOT EXISTS "IDX_notifications_sent_at"
      ON "${schema}"."notifications" ("sent_at");
    `);
  }

  public async down(queryRunner: QueryRunner): Promise<void> {
    const schema = process.env.DB_SCHEMA || 'public';

    await queryRunner.query(`DROP TABLE IF EXISTS "${schema}"."notifications";`);
  }
}
