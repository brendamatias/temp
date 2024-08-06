import { MigrationInterface, QueryRunner } from 'typeorm';

export class CreateInvoices implements MigrationInterface {
  name = 'CreateInvoices';

  public async up(qr: QueryRunner): Promise<void> {
    const schema = process.env.DB_SCHEMA || 'public';

    await qr.query(`CREATE SCHEMA IF NOT EXISTS "${schema}"`);

    await qr.query(`
      CREATE TABLE IF NOT EXISTS "${schema}"."invoices" (
        "id" uuid PRIMARY KEY DEFAULT gen_random_uuid(),
        "partner_id" uuid NOT NULL,
        "invoice_number" varchar(64) NOT NULL,
        "amount" numeric NOT NULL,
        "service_desc" text,
        "reference_month" date NOT NULL,
        "payment_date" date,
        CONSTRAINT "FK_invoices_partner"
          FOREIGN KEY ("partner_id")
          REFERENCES "${schema}"."partners_companies"("id")
          ON DELETE CASCADE
          ON UPDATE NO ACTION
      );
    `);

    await qr.query(`
      CREATE UNIQUE INDEX IF NOT EXISTS "UQ_invoices_invoice_number_partner"
      ON "${schema}"."invoices" ("partner_id", "invoice_number");
    `);
  }

  public async down(qr: QueryRunner): Promise<void> {
    const schema = process.env.DB_SCHEMA || 'public';

    await qr.query(`DROP TABLE IF EXISTS "${schema}"."invoices";`);
  }
}
