import { Controller, Get, Post, Body, Patch, Param, Delete, HttpCode, UseGuards, Query } from '@nestjs/common';
import { ApiTags, ApiBearerAuth, ApiOperation, ApiCreatedResponse, ApiOkResponse, ApiNoContentResponse, ApiNotFoundResponse, ApiParam, ApiQuery } from '@nestjs/swagger';
import { InvoicesService } from './invoices.service';
import { CreateInvoiceDto } from './dto/create-invoice.dto';
import { UpdateInvoiceDto } from './dto/update-invoice.dto';
import { JwtAuthGuard } from 'src/common/guards/jwt-auth.guard';



@ApiTags('invoices')
@ApiBearerAuth()
@UseGuards(JwtAuthGuard)
@Controller('invoices')
export class InvoicesController {
  constructor(private readonly service: InvoicesService) {}

  @Post()
  @ApiOperation({ summary: 'Criar fatura (invoice)' })
  create(@Body() dto: CreateInvoiceDto) {
    return this.service.create(dto);
  }

  @Get()
  @ApiOkResponse({ description: 'Lista de invoices retornada com sucesso.' })
  findAll(@Query('partnerId') partnerId?: string) {
    if (partnerId) return this.service.findByPartner(partnerId);
    return this.service.findAll();
  }

  @Get(':id')
  @ApiOkResponse({ description: 'Invoice encontrado.', type: CreateInvoiceDto })
  findOne(@Param('id') id: string) {
    return this.service.findOne(id);
  }

  @Patch(':id')
  @ApiOkResponse({ description: 'Invoice atualizado com sucesso.', type: CreateInvoiceDto })
  update(@Param('id') id: string, @Body() dto: UpdateInvoiceDto) {
    return this.service.update(id, dto);
  }

  @Delete(':id')
  @ApiNoContentResponse({ description: 'Invoice removido com sucesso.' })
  remove(@Param('id') id: string): Promise<void> {
    return this.service.remove(id);
  }
}