import { MigrationInterface, QueryRunner } from 'typeorm';

export class CreatePartnersCompanies implements MigrationInterface {
  name = 'CreatePartnersCompanies';

  public async up(qr: QueryRunner): Promise<void> {
    const schema = process.env.DB_SCHEMA || 'public';

    await qr.query(`CREATE SCHEMA IF NOT EXISTS "${schema}"`);

    await qr.query(`
      CREATE TABLE IF NOT EXISTS "${schema}"."partners_companies" (
        "id" uuid PRIMARY KEY DEFAULT gen_random_uuid(),
        "tax_id" varchar(32) NOT NULL,
        "company_name" varchar(255) NOT NULL,
        "corporate_name" varchar(255) NOT NULL,
        "notes" text
      );
    `);

    await qr.query(`
      CREATE UNIQUE INDEX IF NOT EXISTS "UQ_partners_companies_tax_id"
      ON "${schema}"."partners_companies" ("tax_id");
    `);
  }

  public async down(qr: QueryRunner): Promise<void> {
    const schema = process.env.DB_SCHEMA || 'public';

    await qr.query(`DROP TABLE IF EXISTS "${schema}"."partners_companies";`);
  }
}
