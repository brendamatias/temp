import { NestFactory, Reflector } from '@nestjs/core';
import { AppModule } from './app.module';
import { ConfigService, ConfigType } from '@nestjs/config';

import { SwaggerModule, DocumentBuilder } from '@nestjs/swagger';

import appConfig from './config/_tests/app.config';
import corsConfig from './config/_tests/cors.config';

import { ClassSerializerInterceptor } from '@nestjs/common';


async function bootstrap() {
  const app = await NestFactory.create(AppModule);

  app.useGlobalInterceptors(new ClassSerializerInterceptor(app.get(Reflector)));

  app.setGlobalPrefix('api');

  const doc = new DocumentBuilder()
    .setTitle('NF Control Activies API')
    .setDescription('OpenAPI docs')
    .setVersion(process.env.npm_package_version ?? '1.0.0')
    .addBearerAuth({
      type: 'http',
      scheme: 'bearer',
      bearerFormat: 'JWT',
      description: 'Send: Bearer <your_token>',
      in: 'header',
      name: 'Authorization'
    })
    .addSecurityRequirements('bearer')
    .addTag('Nest.js')
    .build();

  const document = SwaggerModule.createDocument(app, doc, {
    ignoreGlobalPrefix: false
  });

  SwaggerModule.setup('api/docs', app, document, {
    swaggerOptions: {
      persistAuthorization: true
    },
    customSiteTitle: 'NF API Docs'
  });

  const config = app.get(ConfigService);
  const appCfg = config.get<ConfigType<typeof appConfig>>('app');
  const corsCfg = config.get<ConfigType<typeof corsConfig>>('cors');

  if(corsCfg?.origins === '*') {
    app.enableCors();
  } else if(Array.isArray(corsCfg?.origins)) {
    app.enableCors({ origin: corsCfg.origins });
  }

  await app.listen(appCfg?.port ?? 3000, '0.0.0.0');
}
bootstrap();
