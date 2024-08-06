import { Entity, PrimaryGeneratedColumn, Column, OneToOne, JoinColumn, CreateDateColumn, UpdateDateColumn } from 'typeorm';
import { User } from '../../users/entities/user.entity';

@Entity({ name: 'settings' })
export class Setting {
  @PrimaryGeneratedColumn({ type: 'bigint' })
  id!: string; 

  @OneToOne(() => User, (user) => (user as any).settings, { onDelete: 'CASCADE' })
  @JoinColumn({ name: 'user_id', referencedColumnName: 'id' })
  user!: User;

  @Column({ name: 'user_id', type: 'bigint', unique: true })
  userId!: string;

  @Column({ type: 'numeric', nullable: true })
  mei_annual_limit!: number | null;

  @Column({ type: 'int', nullable: true })
  monthlyReminderDay!: number | null;

  @Column({ type: 'numeric', nullable: true })
  revenueThresholdRatio!: number | null;

  @Column({ type: 'boolean', default: false })
  notifyEmail!: boolean;

  @Column({ type: 'boolean', default: false })
  notifySms!: boolean;

  @CreateDateColumn({ name: 'created_at', type: 'timestamptz' })
  createdAt!: Date;

  @UpdateDateColumn({ name: 'updated_at', type: 'timestamptz' })
  updatedAt!: Date;
}
