import { Entity, PrimaryGeneratedColumn, Column, ManyToOne, JoinColumn } from 'typeorm';
import { ExpenseCategory } from './categorie.entity';
import { Partner } from 'src/columns/partner/entities/partner.entity';


@Entity({ name: 'expenses', schema: process.env.DB_SCHEMA || 'public' })
export class Expense {
  @PrimaryGeneratedColumn('uuid')
  id!: string;

  @Column({ name: 'cathegory_id', type: 'uuid' })
  cathegoryId!: string;

  @ManyToOne(() => ExpenseCategory, { onDelete: 'CASCADE' })
  @JoinColumn({ name: 'cathegory_id', referencedColumnName: 'id' })
  cathegory!: ExpenseCategory;

  @Column({ name: 'partner_id', type: 'uuid' })
  partnerId!: string;

  @ManyToOne(() => Partner, { onDelete: 'CASCADE' })
  @JoinColumn({ name: 'partner_id', referencedColumnName: 'id' })
  partner!: Partner;

  @Column({ type: 'varchar', length: 255 })
  name!: string;

  @Column({ type: 'numeric' })
  amount!: number;

  @Column({ name: 'reference_month', type: 'date' })
  referenceMonth!: string;

  @Column({ name: 'payment_date', type: 'date', nullable: true })
  paymentDate?: string | null;
}