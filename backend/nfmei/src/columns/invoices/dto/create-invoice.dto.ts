import { ApiProperty, ApiPropertyOptional } from '@nestjs/swagger';
import { IsNotEmpty, IsOptional, IsString, IsUUID, IsNumber, IsDateString, Length } from 'class-validator';

export class CreateInvoiceDto {
  @ApiProperty({ description: 'UUID do parceiro', example: 'c1c53b12-1c2d-4ed9-9f9a-3a83b95f1f2a' })
  @IsUUID()
  partnerId!: string;

  @ApiProperty({ description: 'Número da nota fiscal', example: 'NF-2025-001' })
  @IsString()
  @IsNotEmpty()
  @Length(1, 64)
  invoiceNumber!: string;

  @ApiProperty({ description: 'Valor da nota fiscal', example: 1500.75 })
  @IsNumber()
  amount!: number;

  @ApiPropertyOptional({ description: 'Descrição do serviço', example: 'Prestação de serviços de consultoria' })
  @IsOptional()
  @IsString()
  serviceDesc?: string;

  @ApiProperty({ description: 'Mês de referência da nota (YYYY-MM-DD)', example: '2025-08-01' })
  @IsDateString()
  referenceMonth!: string;

  @ApiPropertyOptional({ description: 'Data de pagamento (YYYY-MM-DD)', example: '2025-08-15' })
  @IsOptional()
  @IsDateString()
  paymentDate?: string;
}