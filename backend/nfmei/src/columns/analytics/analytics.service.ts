import { Injectable } from '@nestjs/common';
import { InjectDataSource } from '@nestjs/typeorm';
import { DataSource } from 'typeorm';

@Injectable()
export class AnalyticsService {
  constructor(@InjectDataSource() private readonly ds: DataSource) {}

  private schema = process.env.DB_SCHEMA || 'public';

  async getMeiProgress(year: number, userId: string) {
    const q = `
      with invoice_sum as (
        select coalesce(sum(i.amount), 0) as used
        from "${this.schema}"."invoices" i
        where extract(year from i.reference_month) = $1
      ),
      user_limit as (
        select coalesce(s."mei_annual_limit", 0) as limit
        from "${this.schema}"."settings" s
        where s.user_id = $2
        limit 1
      )
      select
        invoice_sum.used::numeric        as used,
        user_limit.limit::numeric        as limit,
        greatest(user_limit.limit - invoice_sum.used, 0)::numeric as remaining,
        case when user_limit.limit > 0
             then (invoice_sum.used / user_limit.limit)
             else null end::numeric      as ratio
      from invoice_sum, user_limit;
    `;
    const [row] = await this.ds.query(q, [year, userId]);
    return row ?? { used: 0, limit: 0, remaining: 0, ratio: null };
  }

  async getMonthlyInvoices(year: number) {
    const q = `
      with months as (
        select g as m from generate_series(1,12) g
      ),
      data as (
        select extract(month from i.reference_month)::int as m,
               coalesce(sum(i.amount),0)::numeric as total
        from "${this.schema}"."invoices" i
        where extract(year from i.reference_month) = $1
        group by 1
      )
      select months.m as month,
             coalesce(data.total, 0)::numeric as total
      from months
      left join data on data.m = months.m
      order by months.m;
    `;
    return this.ds.query(q, [year]);
  }

  async getMonthlyExpenses(year: number) {
    const q = `
      with months as (
        select g as m from generate_series(1,12) g
      ),
      data as (
        select extract(month from e.reference_month)::int as m,
               coalesce(sum(e.amount),0)::numeric as total
        from "${this.schema}"."expenses" e
        where extract(year from e.reference_month) = $1
        group by 1
      )
      select months.m as month,
             coalesce(data.total, 0)::numeric as total
      from months
      left join data on data.m = months.m
      order by months.m;
    `;
    return this.ds.query(q, [year]);
  }

  async getMonthlyBalance(year: number) {
    const q = `
      with months as (select g as m from generate_series(1,12) g),
      inv as (
        select extract(month from i.reference_month)::int as m,
               coalesce(sum(i.amount),0)::numeric as total
        from "${this.schema}"."invoices" i
        where extract(year from i.reference_month) = $1
        group by 1
      ),
      exp as (
        select extract(month from e.reference_month)::int as m,
               coalesce(sum(e.amount),0)::numeric as total
        from "${this.schema}"."expenses" e
        where extract(year from e.reference_month) = $1
        group by 1
      )
      select
        months.m as month,
        coalesce(inv.total,0)::numeric as invoices_total,
        coalesce(exp.total,0)::numeric as expenses_total,
        (coalesce(inv.total,0) - coalesce(exp.total,0))::numeric as balance
      from months
      left join inv on inv.m = months.m
      left join exp on exp.m = months.m
      order by months.m;
    `;
    return this.ds.query(q, [year]);
  }

  async getExpensesByCategory(year: number, month?: number) {
    const q = `
      with chosen_month as (
        select $2::int as m
      )
      select
        c.id   as category_id,
        c.name as category_name,
        coalesce(sum(e.amount),0)::numeric as total
      from "${this.schema}"."expense_categories" c
      left join "${this.schema}"."expenses" e
        on e.cathegory_id = c.id
       and extract(year  from e.reference_month) = $1
       and extract(month from e.reference_month) = coalesce((select m from chosen_month), extract(month from now()))
      where coalesce(c.is_archived, false) = false
      group by c.id, c.name
      order by c.name;
    `;
    const m = month ?? null;
    return this.ds.query(q, [year, m]);
  }
}
