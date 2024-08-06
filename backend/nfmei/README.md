## Environment Variables Configuration
We use @nestjs/config with Zod validation (fail-fast).
The app will not start if any required variable is missing or invalid.

Copy .env.example to .env and adjust the values.

Keys are grouped by domain:

### Runtime

NODE_ENV: development | test | production

APP_PORT: HTTP server port (default: 3000)

### Database (Postgres)

DB_HOST, DB_PORT, DB_NAME, DB_USER, DB_PASS, DB_SSL

### JWT

JWT_SECRET: required (>=16 chars)
JWT_EXPIRES_IN: e.g. 1d, 12h

### SMTP (Email)

SMTP_HOST, SMTP_PORT, SMTP_USER, SMTP_PASS

### SMS

SMS_PROVIDER: twilio | none
If twilio, provide: SMS_FROM, SMS_ACCOUNT_SID, SMS_AUTH_TOKEN

### CORS

CORS_ORIGINS: * or comma-separated list (http://localhost:4200,https://app.example.com)

## Clone .env.example

```bash
# Copy file and create .env
cp .env.example .env
```

## Compile and run the project

```bash
# development
$ npm run start

# watch mode
$ npm run start:dev

# production mode
$ npm run start:prod
```


### Source tree folders
```
   package.json
   └─ src/
      ├─ app.module.ts
      ├─ main.ts
      ├─ config/
      │  └─ _tests/          
      │  │  └─ env.validation.ts 
      │  ├─ database/         
      │  ├─ typeorm-data-source.ts        
      │  ├─ snake-naming.strategy.ts
      │  └─ seeds/
      │      ├─ seed-runner.ts
      │      └─ seeds.admin-and-settings.ts
      ├─ common/                
      │  ├─ guards/
      │  ├─ interceptors/
      │  ├─ pipes/
      │  ├─ filters/
      │  └─ dto/
      ├─ columns/  
      │  └─ [folder]/
      │     ├─ entities/
      │     ├─ dto/
      │     ├─ [resource].controller.ts
      │     ├─ [resource].service.ts
      │     └─ [resource].module.ts
      ├─ modules/     
      │  └─ health/
      │     ├─ health.controller.ts
      │     └─ health.module.ts
      └─ migrations/

```

