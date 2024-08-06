import { Controller, Get, Post, Body, Patch, Param, Delete, UseGuards, Req } from '@nestjs/common';
import { SettingsService } from './settings.service';
import { CreateSettingDto } from './dto/create-setting.dto';
import { UpdateSettingDto } from './dto/update-setting.dto';
import { ApiBearerAuth, ApiNoContentResponse, ApiOkResponse, ApiTags } from '@nestjs/swagger';
import { JwtAuthGuard } from 'src/common/guards/jwt-auth.guard';

@ApiTags('settings')
@ApiBearerAuth()
@UseGuards(JwtAuthGuard)
@Controller('settings')
export class SettingsController {
  constructor(private readonly settingsService: SettingsService) {}

  @Get('me')
  @ApiOkResponse({ description: 'Configurações do usuário autenticado retornadas com sucesso.' })
  getMe(@Req() req: any) {

    return this.settingsService.findByUserId(String(req.user.userId));
  }

  @Patch('me')
  @ApiOkResponse({ description: 'Configurações atualizadas com sucesso.' })
  updateMe(@Req() req: any, @Body() dto: UpdateSettingDto) {
    return this.settingsService.upsertByUserId(String(req.user.userId), dto);
  }

  @Post()
  @ApiOkResponse({ type: CreateSettingDto })
  create(@Body() createSettingDto: CreateSettingDto) {
    return this.settingsService.create(createSettingDto);
  }

  @Get()
  @ApiOkResponse({ type: [ CreateSettingDto ] })
  findAll() {
    return this.settingsService.findAll();
  }

  @Get(':id')
  @ApiOkResponse({ type: CreateSettingDto })
  findOne(@Param('id') id: string) {
    return this.settingsService.findOne(+id);
  }

  @Patch(':id')
  @ApiOkResponse({ type: CreateSettingDto })
  update(@Param('id') id: string, @Body() updateSettingDto: UpdateSettingDto) {
    return this.settingsService.update(+id, updateSettingDto);
  }

  @Delete(':id')
  @ApiNoContentResponse({ description: 'Settings removido com sucesso.' })
  remove(@Param('id') id: string) {
    return this.settingsService.remove(+id);
  }
}