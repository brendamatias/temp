import { Expose } from 'class-transformer';
import { ApiProperty, ApiPropertyOptional } from '@nestjs/swagger';

export class UserResponseDto {
  @ApiProperty({ example: '1' })
  @Expose()
  id!: string;

  @ApiProperty({ example: 'admin@local.dev' })
  @Expose()
  email!: string;

  @ApiPropertyOptional({ example: 'Admin Local' })
  @Expose()
  fullName?: string | null;

  @ApiPropertyOptional({ example: '+5511987654321' })
  @Expose()
  phone?: string | null;

  @ApiProperty({ example: 'user' })
  @Expose()
  role!: string;

  @ApiProperty({ example: true })
  @Expose()
  isActive!: boolean;

  @ApiProperty()
  @Expose()
  createdAt!: Date;

  @ApiProperty()
  @Expose()
  updatedAt!: Date;
}