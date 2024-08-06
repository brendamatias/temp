import { ApiProperty, ApiPropertyOptional } from '@nestjs/swagger';
import { IsNotEmpty, IsOptional, IsString, Length } from 'class-validator';

export class CreatePartnerDto {
  @ApiProperty({ description: 'Documento fiscal da empresa (CNPJ/CPF)', example: '12345678000190' })
  @IsString()
  @IsNotEmpty()
  @Length(1, 32)
  taxId!: string;

  @ApiProperty({ description: 'Nome fantasia da empresa', example: 'Minha Startup LTDA' })
  @IsString()
  @IsNotEmpty()
  @Length(1, 255)
  companyName!: string;

  @ApiProperty({ description: 'Razão social da empresa', example: 'Minha Startup Serviços de TI LTDA' })
  @IsString()
  @IsNotEmpty()
  @Length(1, 255)
  corporateName!: string;

  @ApiPropertyOptional({ description: 'Observações sobre a empresa', example: 'Cliente estratégico, precisa de atenção especial' })
  @IsOptional()
  @IsString()
  notes?: string;
}