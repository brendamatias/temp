import { MigrationInterface, QueryRunner } from 'typeorm';

export class CreateAnalyticsViews implements MigrationInterface {
  name = 'CreateAnalyticsViews';

  public async up(qr: QueryRunner): Promise<void> {
    const schema = process.env.DB_SCHEMA || 'public';
    
    await qr.query(`CREATE SCHEMA IF NOT EXISTS "${schema}"`);

    await qr.query(`
      CREATE OR REPLACE VIEW "${schema}"."vw_invoices_monthly" AS
      select
        extract(year from i.reference_month)::int as year,
        extract(month from i.reference_month)::int as month,
        sum(i.amount)::numeric as total
      from "${schema}"."invoices" i
      group by 1,2;
    `);

    await qr.query(`
      CREATE OR REPLACE VIEW "${schema}"."vw_expenses_monthly" AS
      select
        extract(year from e.reference_month)::int as year,
        extract(month from e.reference_month)::int as month,
        sum(e.amount)::numeric as total
      from "${schema}"."expenses" e
      group by 1,2;
    `);

    await qr.query(`
      CREATE OR REPLACE VIEW "${schema}"."vw_expenses_by_category_monthly" AS
      select
        extract(year from e.reference_month)::int as year,
        extract(month from e.reference_month)::int as month,
        c.id as category_id,
        c.name as category_name,
        sum(e.amount)::numeric as total
      from "${schema}"."expense_categories" c
      left join "${schema}"."expenses" e on e.cathegory_id = c.id
      group by 1,2,c.id,c.name;
    `);
  }

  public async down(qr: QueryRunner): Promise<void> {
    const schema = process.env.DB_SCHEMA || 'public';
    await qr.query(`DROP VIEW IF EXISTS "${schema}"."vw_invoices_monthly";`);
    await qr.query(`DROP VIEW IF EXISTS "${schema}"."vw_expenses_monthly";`);
    await qr.query(`DROP VIEW IF EXISTS "${schema}"."vw_expenses_by_category_monthly";`);
  }
}
