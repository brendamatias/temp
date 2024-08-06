import { ApiProperty, ApiPropertyOptional } from '@nestjs/swagger';
import { IsNotEmpty, IsOptional, IsString, IsUUID, IsNumber, IsDateString, Length } from 'class-validator';

export class CreateExpenseDto {
  @ApiProperty({ description: 'UUID da categoria', example: 'b4d3a86a-2a9a-4aef-9cb3-83dc90f9d9a1' })
  @IsUUID()
  cathegoryId!: string;

  @ApiProperty({ description: 'UUID do parceiro', example: 'c1c53b12-1c2d-4ed9-9f9a-3a83b95f1f2a' })
  @IsUUID()
  partnerId!: string;

  @ApiProperty({ description: 'Nome da despesa', example: 'Conta de energia' })
  @IsString()
  @IsNotEmpty()
  @Length(1, 255)
  name!: string;

  @ApiProperty({ description: 'Valor da despesa', example: 450.75 })
  @IsNumber()
  amount!: number;

  @ApiProperty({ description: 'Mês de referência da despesa (YYYY-MM-DD)', example: '2025-08-01' })
  @IsDateString()
  referenceMonth!: string;

  @ApiPropertyOptional({ description: 'Data do pagamento (YYYY-MM-DD)', example: '2025-08-10' })
  @IsOptional()
  @IsDateString()
  paymentDate?: string;
}