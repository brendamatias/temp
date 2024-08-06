import { NotificationChannel, NotificationPayload } from "src/common/types/notification.types";
import { Column, Entity, PrimaryGeneratedColumn } from "typeorm";

@Entity({ name: 'notifications' })
export class Notification {
  @PrimaryGeneratedColumn('uuid')
  id!: string;

  @Column({ type: 'varchar', length: 64 })
  type!: string;

  @Column({ type: 'varchar', length: 16 })
  channel!: NotificationChannel; 

  @Column({ type: 'jsonb', default: () => `'{}'::jsonb` })
  payload!: NotificationPayload;

  @Column({ name: 'sent_at', type: 'timestamptz', default: () => 'now()' })
  sentAt!: Date;
}