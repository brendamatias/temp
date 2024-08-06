import { Entity, PrimaryGeneratedColumn, Column } from 'typeorm';

@Entity({ name: 'partners_companies', schema: process.env.DB_SCHEMA || 'public' })
export class Partner {
  @PrimaryGeneratedColumn('uuid')
  id!: string;

  @Column({ name: 'tax_id', type: 'varchar', length: 32, unique: true })
  taxId!: string;

  @Column({ name: 'company_name', type: 'varchar', length: 255 })
  companyName!: string;

  @Column({ name: 'corporate_name', type: 'varchar', length: 255 })
  corporateName!: string;

  @Column({ name: 'notes', type: 'text', nullable: true })
  notes?: string | null;
}