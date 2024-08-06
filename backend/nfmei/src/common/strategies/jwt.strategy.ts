import { Injectable } from "@nestjs/common";
import { ConfigService } from "@nestjs/config";
import { PassportStrategy } from "@nestjs/passport";
import { ExtractJwt, Strategy } from "passport-jwt";

@Injectable()
export class JwtStrategy extends PassportStrategy(Strategy, 'jwt') {
  constructor(cfg: ConfigService) {
    super({
      jwtFromRequest: ExtractJwt.fromAuthHeaderAsBearerToken(),
      secretOrKey: cfg.getOrThrow<string>('JWT_SECRET', { infer: true }),
      ignoreExpiration: false
    });
  }

  async validate(payload: { sub: string, email: string, role: string }) {
    return {
      userId: payload.sub,
      email: payload.email,
      role: payload.role
    }
  }
}