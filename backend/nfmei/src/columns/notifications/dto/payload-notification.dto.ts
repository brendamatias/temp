import { ApiPropertyOptional } from '@nestjs/swagger';
import { IsNumber, IsOptional, IsString } from 'class-validator';
import { Type } from 'class-transformer';

export class NotificationPayloadDto {
  @ApiPropertyOptional({ description: 'Percentual utilizado do limite MEI', example: 82.5 })
  @IsOptional()
  @Type(() => Number)
  @IsNumber()
  percentUsed?: number;

  @ApiPropertyOptional({ description: 'Limite anual do MEI', example: 81000 })
  @IsOptional()
  @Type(() => Number)
  @IsNumber()
  mei_annual_limit?: number;

  @ApiPropertyOptional({ description: 'Instruções para o usuário', example: 'Evite emitir novas NFs este mês.' })
  @IsOptional()
  @IsString()
  instructions?: string;
}