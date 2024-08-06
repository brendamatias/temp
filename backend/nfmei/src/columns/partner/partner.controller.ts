import { Controller, Get, Post, Body, Patch, Param, Delete, UseGuards } from '@nestjs/common';
import { PartnerService } from './partner.service';
import { CreatePartnerDto } from './dto/create-partner.dto';
import { UpdatePartnerDto } from './dto/update-partner.dto';
import { ApiBearerAuth, ApiNoContentResponse, ApiOkResponse, ApiTags } from '@nestjs/swagger';
import { JwtAuthGuard } from 'src/common/guards/jwt-auth.guard';

@ApiTags('partner')
@ApiBearerAuth()
@UseGuards(JwtAuthGuard)
@Controller('partner')
export class PartnerController {
constructor(private readonly service: PartnerService) {}

  @Post()
  @ApiOkResponse({ type: CreatePartnerDto })
  create(@Body() dto: CreatePartnerDto) {
    return this.service.create(dto);
  }

  @Get()
  @ApiOkResponse({ description: 'Lista de empresas parceiras retornada com sucesso.' })
  findAll() {
    return this.service.findAll();
  }

  @Get(':id')
  @ApiOkResponse({ description: 'Empresa parceira encontrada.', type: CreatePartnerDto })
  findOne(@Param('id') id: string) {
    return this.service.findOne(id);
  }

  @Patch(':id')
  @ApiOkResponse({ description: 'Empresa parceira atualizada com sucesso.', type: UpdatePartnerDto })
  update(@Param('id') id: string, @Body() dto: UpdatePartnerDto) {
    return this.service.update(id, dto);
  }

  @Delete(':id')
  @ApiNoContentResponse({ description: 'Empresa parceira removida com sucesso.' })
  remove(@Param('id') id: string) {
    return this.service.remove(id);
  }
}
