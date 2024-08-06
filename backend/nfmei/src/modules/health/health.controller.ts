import { Controller, Get } from '@nestjs/common';
import { ConfigService } from '@nestjs/config';
import { ApiBearerAuth } from '@nestjs/swagger';

@Controller('health')
export class HealthController {
  constructor(private readonly config: ConfigService) {}

  @Get()
  getHealth() {
    return {
      status: 'ok',
      version: this.config.getOrThrow<string>('APP_VERSION') ?? '0.0.0'
    }
  }

  @Get('bearer')
  @ApiBearerAuth('bearer')
  getHealthBearer() {
    return {
      status: 'ok',
      version: this.config.getOrThrow<string>('APP_VERSION') ?? '0.0.0'
    }
  }
}
