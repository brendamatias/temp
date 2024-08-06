import { ApiProperty, ApiPropertyOptional } from '@nestjs/swagger';
import { IsBoolean, IsInt, IsNumber, IsOptional, IsString, Max, Min } from 'class-validator';
import { Type } from 'class-transformer';

export class CreateSettingDto {
  @ApiProperty({ description: 'ID do usuário', type: String, example: '1' })
  @IsString()
  userId!: string;

  @ApiPropertyOptional({ description: 'Limite anual do MEI', example: 81600 })
  @IsOptional()
  @Type(() => Number)
  @IsNumber()
  mei_annual_limit?: number;

  @ApiPropertyOptional({ description: 'Dia do lembrete mensal (1–31)', example: 25 })
  @IsOptional()
  @Type(() => Number)
  @IsInt()
  @Min(1)
  @Max(31)
  monthlyReminderDay?: number;

  @ApiPropertyOptional({ description: 'Razão limiar de faturamento (0–1)', example: 0.8 })
  @IsOptional()
  @Type(() => Number)
  @IsNumber()
  @Min(0)
  @Max(1)
  revenueThresholdRatio?: number;

  @ApiPropertyOptional({ description: 'Notificar por e-mail', default: false })
  @IsOptional()
  @IsBoolean()
  notifyEmail?: boolean;

  @ApiPropertyOptional({ description: 'Notificar por SMS', default: false })
  @IsOptional()
  @IsBoolean()
  notifySms?: boolean;
}