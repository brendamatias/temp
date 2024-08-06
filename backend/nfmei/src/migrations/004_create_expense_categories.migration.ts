import { MigrationInterface, QueryRunner } from 'typeorm';

export class CreateExpenseCategories implements MigrationInterface {
  name = 'CreateExpenseCategories';

  public async up(qr: QueryRunner): Promise<void> {
    const schema = process.env.DB_SCHEMA || 'public';

    await qr.query(`CREATE SCHEMA IF NOT EXISTS "${schema}"`);

    await qr.query(`
      CREATE TABLE IF NOT EXISTS "${schema}"."expense_categories" (
        "id" uuid PRIMARY KEY DEFAULT gen_random_uuid(),
        "name" varchar(255) NOT NULL,
        "description" text,
        "is_archived" boolean NOT NULL DEFAULT false
      );
    `);

    await qr.query(`
      CREATE UNIQUE INDEX IF NOT EXISTS "UQ_expense_categories_tax_name"
      ON "${schema}"."expense_categories" ("name");
    `);
  }

  public async down(qr: QueryRunner): Promise<void> {
    const schema = process.env.DB_SCHEMA || 'public';

    await qr.query(`DROP TABLE IF EXISTS "${schema}"."expense_categories";`);
  }
}
