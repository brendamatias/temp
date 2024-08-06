import { Entity, PrimaryGeneratedColumn, Column } from 'typeorm';

@Entity({ name: 'expense_categories', schema: process.env.DB_SCHEMA || 'public' })
export class ExpenseCategory {
  @PrimaryGeneratedColumn('uuid')
  id!: string;

  @Column({ name: 'name', type: 'varchar', length: 255 })
  name!: string;

  @Column({ name: 'description', type: 'text', nullable: true })
  description?: string | null;

  @Column({ name: 'is_archived', type: 'boolean', default: false })
  isArchived!: boolean;
}