import { Controller, Get, Post, Body, Patch, Param, Delete, UseGuards, Query, Req } from '@nestjs/common';
import { ApiTags, ApiBearerAuth, ApiCreatedResponse, ApiOkResponse, ApiNoContentResponse, ApiQuery } from '@nestjs/swagger';
import { NotificationsService } from './notifications.service';
import { CreateNotificationDto } from './dto/create-notification.dto';
import { UpdateNotificationDto } from './dto/update-notification.dto';
import { JwtAuthGuard } from 'src/common/guards/jwt-auth.guard';

@ApiTags('notifications')
@ApiBearerAuth()
@UseGuards(JwtAuthGuard)
@Controller('notifications')
export class NotificationsController {
  constructor(private readonly service: NotificationsService) {}

  @Post('job/threshold-check')
  @ApiCreatedResponse({ description: 'Threshold check executed.' })
  runThreshold(@Req() req: any) {
    const year = new Date().getFullYear();
    return this.service.send(String(req.user.userId), year, 'EMAIL');
  }

  @Post()
  @ApiOkResponse({ type: CreateNotificationDto })
  create(@Body() dto: CreateNotificationDto) {
    return this.service.create(dto);
  }

  @Get()
  @ApiOkResponse({ description: 'Lista de notificações retornada.' })
  @ApiQuery({ name: 'channel', required: false, enum: ['SMS', 'EMAIL'] })
  @ApiQuery({ name: 'type', required: false, description: 'Tipo da notificação (ex.: LIMIT_WARNING)' })
  @ApiQuery({ name: 'dateFrom', required: false, description: 'ISO datetime inicial' })
  @ApiQuery({ name: 'dateTo', required: false, description: 'ISO datetime final' })
  findAll(
    @Query('channel') channel?: 'SMS' | 'EMAIL',
    @Query('type') type?: string,
    @Query('dateFrom') dateFrom?: string,
    @Query('dateTo') dateTo?: string,
  ) {
    return this.service.findAll({ channel, type, dateFrom, dateTo });
  }

  @Get('dry-run')
  @ApiOkResponse({ description: 'Dry-run payload returned.' })
  @ApiQuery({ name: 'year', required: true, type: Number })
  @ApiQuery({ name: 'channel', required: true, enum: ['SMS','EMAIL'] })
  dryRun(@Req() req: any, @Query('year') year: string, @Query('channel') channel: 'SMS'|'EMAIL') {
    return this.service.dryRun(String(req.user.userId), parseInt(year, 10), channel);
  }

  @Post('send')
  @ApiOkResponse({ description: 'Send and persist notification (history)' })
  @ApiQuery({ name: 'year', required: true, type: Number })
  @ApiQuery({ name: 'channel', required: true, enum: ['SMS','EMAIL'] })
  send(@Req() req: any, @Query('year') year: string, @Query('channel') channel: 'SMS'|'EMAIL') {
    return this.service.send(String(req.user.userId), parseInt(year, 10), channel);
  }

  @Get(':id')
  @ApiOkResponse({ type: CreateNotificationDto })
  findOne(@Param('id') id: string) {
    return this.service.findOne(id);
  }

  @Patch(':id')
  @ApiOkResponse({ type: CreateNotificationDto })
  update(@Param('id') id: string, @Body() dto: UpdateNotificationDto) {
    return this.service.update(id, dto);
  }

  @Delete(':id')
  @ApiNoContentResponse({ description: 'Notificação removida com sucesso.' })
  remove(@Param('id') id: string): Promise<void> {
    return this.service.remove(id);
  }
}
