import { DataSource } from 'typeorm';
import * as bcrypt from 'bcryptjs';

export async function seedAdminAndSettings(ds: DataSource) {
  const qr = ds.createQueryRunner();
  await qr.connect();
  await qr.startTransaction();

  try {
    const adminEmail = process.env.SEED_ADMIN_EMAIL || 'admin@local.dev';
    const adminPass  = process.env.SEED_ADMIN_PASS  || 'admin123';
    const hash = await bcrypt.hash(adminPass, 10);

    await qr.query(
      `
      INSERT INTO "users" ("email","password","role","is_active")
      VALUES ($1, $2, 'admin', true)
      ON CONFLICT ("email") DO NOTHING
      `,
      [adminEmail, hash],
    );

    const defaults: Array<{ key: string; value: any }> = [
      { key: 'app.name', value: 'NF Control Activities (Local)' },
      { key: 'auth.jwt.expires', value: '7d' },
      { key: 'auth.password.minLength', value: 8 },
    ];

    for (const item of defaults) {
      await qr.query(
        `
        INSERT INTO "settings" ("key","value")
        VALUES ($1, $2::jsonb)
        ON CONFLICT ("key") DO NOTHING
        `,
        [item.key, JSON.stringify(item.value)],
      );
    }

    await qr.commitTransaction();
  } catch (e) {
    await qr.rollbackTransaction();
    throw e;
  } finally {
    await qr.release();
  }
}