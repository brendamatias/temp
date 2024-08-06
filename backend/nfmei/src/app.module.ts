import { Module } from '@nestjs/common';
import { ConfigModule, ConfigService } from '@nestjs/config';
import { HealthModule } from './modules/health/health.module';

import { TypeOrmModule } from '@nestjs/typeorm';

import * as packageJson from '../package.json';
import { Env, validateEnv } from './config/_tests/env.validation';


import appConfig from './config/_tests/app.config';
import dbConfig from './config/_tests/db.config';
import jwtConfig from './config/_tests/jwt.config';
import smptConfig from './config/_tests/smpt.config';
import smsConfig from './config/_tests/sms.config';
import corsConfig from './config/_tests/cors.config';

import { SnakeNamingStrategy } from './config/database/snake-naming.strategy';
import { LoggerOptions } from 'typeorm';
import { AuthModule } from './columns/auth/auth.module';
import { SettingsModule } from './columns/settings/settings.module';
import { PartnerModule } from './columns/partner/partner.module';
import { ExpenseCategoriesModule } from './columns/categorie/categorie.module';
import { InvoicesModule } from './columns/invoices/invoices.module';
import { AnalyticsModule } from './columns/analytics/analytics.module';
import { NotificationsModule } from './columns/notifications/notifications.module';
import { ScheduleModule } from '@nestjs/schedule';

@Module({
  imports: [
    ScheduleModule.forRoot(),
    ConfigModule.forRoot({
      isGlobal: true,
      envFilePath: ['.env'],
      load: [
        () => ({
          APP_VERSION: (packageJson as any).version
        }),
        appConfig,
        dbConfig,
        jwtConfig,
        smptConfig,
        smsConfig,
        corsConfig
      ],
      validate: validateEnv,
    }),
    TypeOrmModule.forRootAsync({
      inject: [ConfigService],
      useFactory: (cfg: ConfigService<Env, true>) => {
        const sslEnabled = String(cfg.get<boolean>('DB_SSL', { infer: true })).toLocaleLowerCase() === 'true';

        const logging: LoggerOptions =
          cfg.get('NODE_ENV', { infer: true }) === 'development'
            ? ['error', 'schema'] 
            : ['error'];

        const common = {
          type: 'postgres' as const,
          autoLoadEntities: true,
          synchronize: false, 
          logging,
          namingStrategy: new SnakeNamingStrategy(),
          ssl: sslEnabled ? { rejectUnauthorized: false } : false,
        }

        const dbUrl = cfg.get<string | undefined>('DATABASE_URL' as any);
        if (dbUrl) {
          return { ...common, url: dbUrl };
        }
        
        return {
          ...common,
          host: cfg.get<string>('DB_HOST', { infer: true }),
          port: cfg.get<number>('DB_PORT', { infer: true }),
          username: cfg.get<string>('DB_USER', { infer: true }),
          password: cfg.get<string>('DB_PASS', { infer: true }),
          database: cfg.get<string>('DB_NAME', { infer: true }),
        };
      }
    }),
    HealthModule,
    AuthModule,
    SettingsModule,
    PartnerModule,
    ExpenseCategoriesModule,
    InvoicesModule,
    AnalyticsModule,
    NotificationsModule
  ],
  controllers: [],
  providers: [],
})
export class AppModule {}
