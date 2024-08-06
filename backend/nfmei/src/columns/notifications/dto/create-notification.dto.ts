import { ApiProperty, ApiPropertyOptional } from '@nestjs/swagger';
import { IsIn, IsISO8601, IsOptional, IsString, ValidateNested } from 'class-validator';
import { Type } from 'class-transformer';
import { NotificationPayloadDto } from './payload-notification.dto';

export class CreateNotificationDto {
  @ApiProperty({ description: 'Tipo da notificação', example: 'LIMIT_WARNING' })
  @IsString()
  type!: string;

  @ApiProperty({ description: 'Canal de envio', enum: ['SMS', 'EMAIL'] })
  @IsString()
  @IsIn(['SMS', 'EMAIL'])
  channel!: 'SMS' | 'EMAIL';

  @ApiPropertyOptional({ description: 'Dados adicionais da notificação (JSON)' , type: NotificationPayloadDto })
  @IsOptional()
  @ValidateNested()
  @Type(() => NotificationPayloadDto)
  payload?: NotificationPayloadDto;

  @ApiPropertyOptional({ description: 'Data/hora de envio (ISO 8601)', example: '2025-09-01T12:00:00Z' })
  @IsOptional()
  @IsISO8601()
  sentAt?: string; // opcional; se não enviar, o banco usa now()
}