import { Injectable } from '@nestjs/common';
import { JwtService } from '@nestjs/jwt';

@Injectable()
export class AuthService {
  constructor(
    private readonly jwt: JwtService
  ) {}

  async login(user: { id?: string; userId?: string; email: string; role: string }) {
    const sub = user.id ?? user.userId!;
    const payload = {
      sub,
      email: user.email,
      role: user.role
    }

    return {
      access_token: await this.jwt.signAsync(payload),
    }
  }
}
