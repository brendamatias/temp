import { Partner } from 'src/columns/partner/entities/partner.entity';
import {
  Entity,
  PrimaryGeneratedColumn,
  Column,
  ManyToOne,
  JoinColumn,
} from 'typeorm';


@Entity({ name: 'invoices', schema: process.env.DB_SCHEMA || 'public' })
export class Invoice {
  @PrimaryGeneratedColumn('uuid')
  id!: string;

  @Column({ name: 'partner_id', type: 'uuid' })
  partnerId!: string;

  @ManyToOne(() => Partner, { onDelete: 'CASCADE' })
  @JoinColumn({ name: 'partner_id', referencedColumnName: 'id' })
  partner!: Partner;

  @Column({ name: 'invoice_number', type: 'varchar', length: 64 })
  invoiceNumber!: string;

  @Column({ type: 'numeric' })
  amount!: number;

  @Column({ name: 'service_desc', type: 'text', nullable: true })
  serviceDesc?: string | null;

  @Column({ name: 'reference_month', type: 'date' })
  referenceMonth!: string; 

  @Column({ name: 'payment_date', type: 'date', nullable: true })
  paymentDate?: string | null; 
}