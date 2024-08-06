import 'dotenv/config';
import dataSource from '../typeorm-data-source';
import { seedAdminAndSettings } from '../seeds-admin-n-settings';

async function bootstrap() {
  await dataSource.initialize();
  try {
    await seedAdminAndSettings(dataSource);
    console.log('✅ Seed finalizado com sucesso.');
  } finally {
    await dataSource.destroy();
  }
}

bootstrap().catch((err) => {
  console.error('❌ Seed falhou:', err);
  process.exit(1);
});