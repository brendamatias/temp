import { ApiHideProperty } from "@nestjs/swagger";
import { Exclude } from "class-transformer";
import { Column, CreateDateColumn, Entity, PrimaryGeneratedColumn, UpdateDateColumn } from "typeorm";

@Entity({ name: 'users' })
export class User {
  @PrimaryGeneratedColumn('increment', { type: 'bigint'})
  id!: string;

  @Column({ type: 'varchar', length: 255, unique: true })
  email!: string;

  @Column({ name: 'full_name', type: 'varchar', length: 255, nullable: true })
  fullName?: string | null;

  @Column({ name: 'phone', type: 'varchar', length: 32, nullable: true, unique: true })
  phone?: string | null;

  @ApiHideProperty()
  @Exclude({ toPlainOnly: true })
  @Column({ name: 'password', type: 'varchar', length: 255 })
  password!: string;

  @Column({ type: 'varchar', length: 32, default: 'user' })
  role!: string;

  @Column({ name: 'is_active', type: 'boolean', default: true })
  isActive!: boolean;

  @CreateDateColumn({ name: 'created_at', type: 'timestamptz'})
  createdAt!: Date;

  @CreateDateColumn({ name: 'updated_at', type: 'timestamptz'})
  updatedAt!: Date;
}
