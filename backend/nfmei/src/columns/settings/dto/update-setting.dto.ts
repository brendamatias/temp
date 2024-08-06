import { PartialType } from '@nestjs/swagger';
import { ApiPropertyOptional } from '@nestjs/swagger';
import { IsOptional, IsString } from 'class-validator';
import { CreateSettingDto } from './create-setting.dto';

export class UpdateSettingDto extends PartialType(CreateSettingDto) {
  @ApiPropertyOptional({ description: 'ID do usuário (normalmente não alterado)' })
  @IsOptional()
  @IsString()
  userId?: string;
}