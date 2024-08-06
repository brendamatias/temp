import { MigrationInterface, QueryRunner } from 'typeorm';

export class CreateExpenses implements MigrationInterface {
  name = 'CreateExpenses';

  public async up(qr: QueryRunner): Promise<void> {
    const schema = process.env.DB_SCHEMA || 'public';

    await qr.query(`CREATE SCHEMA IF NOT EXISTS "${schema}"`);

    await qr.query(`
      CREATE TABLE IF NOT EXISTS "${schema}"."expenses" (
        "id" uuid PRIMARY KEY DEFAULT gen_random_uuid(),
        "cathegory_id" uuid NOT NULL,
        "partner_id" uuid NOT NULL,
        "name" varchar(255) NOT NULL,
        "amount" numeric NOT NULL,
        "reference_month" date NOT NULL,
        "payment_date" date,
        CONSTRAINT "FK_expenses_cathegory"
          FOREIGN KEY ("cathegory_id")
          REFERENCES "${schema}"."expense_categories"("id")
          ON DELETE CASCADE
          ON UPDATE NO ACTION,
        CONSTRAINT "FK_expenses_partner"
          FOREIGN KEY ("partner_id")
          REFERENCES "${schema}"."partners_companies"("id")
          ON DELETE CASCADE
          ON UPDATE NO ACTION
      );
    `);

    await qr.query(`
      CREATE INDEX IF NOT EXISTS "IDX_expenses_reference_month"
      ON "${schema}"."expenses" ("reference_month");
    `);
  }

  public async down(qr: QueryRunner): Promise<void> {
    const schema = process.env.DB_SCHEMA || 'public';

    await qr.query(`DROP TABLE IF EXISTS "${schema}"."expenses";`);
  }
}
