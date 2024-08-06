import { z } from 'zod';

export const envSchema = z.object({
  //VERSIONS
  NODE_ENV: z.enum(['development', 'test', 'production']).default('development'),
  APP_PORT: z.coerce.number().int().positive().default(3000),

  //DB (POSTGRESQL)
  DB_URL: z.string().optional(),
  DB_HOST: z.string().min(1),
  DB_PORT: z.coerce.number().int().positive().default(5432),
  DB_NAME: z.string().min(1),
  DB_USER: z.string().min(1),
  DB_PASS: z.string().min(1),
  DB_SSL:  z.enum(['true', 'false']).default('false'),

  //SEEDS
  SEED_ADMIN_EMAIL: z.string().min(1).default('admin@local.dev'),
  SEED_ADMIN_PASS: z.string().min(6).default('admin123'),
  SEED_USERS_TABLE: z.string().min(1).default('users'),
  SEED_SETTINGS_TABLE: z.string().min(1).default('settings'),
  ALLOW_PROD_SEED: z.coerce.boolean().default(false),

  //JWT 
  JWT_SECRET: z.string().min(1),
  JWT_EXPIRES_IN: z.string().default('1d'),

  //SMTP (email)
  SMTP_HOST: z.string().optional(),
  SMTP_PORT: z.coerce.number().int().optional(),
  SMTP_USER: z.string().optional(),
  SMTP_PASS: z.string().optional(),

  //SMS
  SMS_PROVIDER: z.enum(['twilio', 'none']).default('none'),
  SMS_FROM: z.string().optional(),
  SMS_ACCOUNT_SID: z.string().optional(),
  SMS_AUTH_TOKEN: z.string().optional(),

  //CORS
  CORS_ORIGINS: z.string().default('*')
}).superRefine((val, ctx) => {
  //RULES DB
    if (!val.DB_URL) {
    const missing: string[] = [];
    if (!val.DB_HOST) missing.push('DB_HOST');
    if (!val.DB_PORT) missing.push('DB_PORT');
    if (!val.DB_USER) missing.push('DB_USER');
    if (!val.DB_PASS) missing.push('DB_PASS');
    if (!val.DB_NAME) missing.push('DB_NAME');

    if (missing.length) {
      ctx.addIssue({
        code: 'custom',
        message: `Defina DATABASE_URL ou todas as variáveis: ${missing.join(', ')}`,
        path: ['DATABASE_URL'],
      });
    }
  }
});

export type Env = z.infer<typeof envSchema>;

export function validateEnv(config: Record<string, unknown>): Env {
  const parsed = envSchema.safeParse(config);

  if(!parsed.success) {
    const errs = parsed.error.issues.map(i => `${i.path.join('.')}: ${i.message}`).join('\n -');
    throw new Error(`Environment validation error: \n - ${errs}`);
  }

  if(parsed.data.SMS_PROVIDER !== 'none') {
    const missing: string[] = [];
    if(!parsed.data.SMS_FROM) missing.push('SMS_FROM');
    if(!parsed.data.SMS_ACCOUNT_SID) missing.push('SMS_ACCOUNT_SID');
    if(!parsed.data.SMS_AUTH_TOKEN) missing.push('SMS_AUTH_TOKEN');
    if(missing.length) {
      throw new Error(`Environment validation error (SMS): missing ${missing.join(', ')}`);
    }
  }

  return parsed.data;
}