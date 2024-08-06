import { ApiProperty, ApiPropertyOptional } from '@nestjs/swagger';
import { IsBoolean, IsNotEmpty, IsOptional, IsString, Length } from 'class-validator';

export class CreateExpenseCategoryDto {
  @ApiProperty({ description: 'Nome da categoria', example: 'Alimentação' })
  @IsString()
  @IsNotEmpty()
  @Length(1, 255)
  name!: string;

  @ApiPropertyOptional({ description: 'Descrição da categoria', example: 'Gastos com alimentação e bebidas' })
  @IsOptional()
  @IsString()
  description?: string;

  @ApiPropertyOptional({ description: 'Indica se a categoria está arquivada', default: false })
  @IsOptional()
  @IsBoolean()
  isArchived?: boolean;
}