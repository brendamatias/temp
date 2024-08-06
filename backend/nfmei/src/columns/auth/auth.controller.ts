import { Controller, Post, UseGuards, Request, Get } from '@nestjs/common';
import { AuthService } from './auth.service';
import { LocalAuthGuard } from 'src/common/guards/local-auth.guards';
import { ApiBearerAuth, ApiBody, ApiOkResponse, ApiTags } from '@nestjs/swagger';
import { LoginDto } from '../users/dto/login.dto';
import { JwtAuthGuard } from 'src/common/guards/jwt-auth.guard';

@ApiTags('auth')
@Controller('auth')
export class AuthController {
  constructor(private readonly auth: AuthService) {}

  @UseGuards(LocalAuthGuard)
  @Post('login')
  @ApiBody({ type: LoginDto })
  @ApiOkResponse({ schema: { properties: { access_token: { type: 'string' } } } })
  async login(@Request() req: any) {
    return this.auth.login(req.user);
  }

  @UseGuards(JwtAuthGuard)
  @Get('profile')
  @ApiBearerAuth('bearer')
  @ApiOkResponse({ schema: { example: { userId: '1', email: 'admin@local.dev', role: 'admin' } }})
  async profile(@Request() req: any) {
    return req.user;
  }
}
