import { Injectable, UnauthorizedException } from "@nestjs/common";
import { PassportStrategy } from "@nestjs/passport";
import { Strategy } from "passport-local";
import { UsersService } from "src/columns/users/users.service";
import * as bcrypt from 'bcryptjs';

@Injectable()
export class LocalStrategy extends PassportStrategy(Strategy, 'local') {
  constructor(private readonly users: UsersService) {
    super({
      usernameField: 'email',
      passwordField: 'password'
    });
  }

  async validate(email: string, password: string) {
    const user = await this.users.findByEmail(email);
    
    if(!user || !user.isActive) {
      throw new UnauthorizedException('Invalid credentials');
    }

    const ok = await bcrypt.compare(password, user.password);
    if(!ok) throw new UnauthorizedException('Invalid credentials');
    return this.users.strip(user);
  }
}