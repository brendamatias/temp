import { Injectable, Logger } from '@nestjs/common';
import { Cron, CronExpression, Timeout } from '@nestjs/schedule';
import { DataSource } from 'typeorm';
import { NotificationsService } from '../notifications.service';

const TZ = 'America/Sao_Paulo';

@Injectable()
export class NotificationsScheduler {
  private readonly logger = new Logger(NotificationsScheduler.name);
  private readonly schema = process.env.DB_SCHEMA || 'public';

  constructor(
    private readonly ds: DataSource,
    private readonly notifications: NotificationsService,
  ) {}

  private async getNotifiableUsers(): Promise<{ id: string; monthlyReminderDay: number | null }[]> {
    const q = `
      select s.user_id::text as id,
             coalesce(s."monthlyReminderDay", 1) as "monthlyReminderDay"
      from "${this.schema}"."settings" s
      where coalesce(s."notifyEmail", false) = true
         or coalesce(s."notifySms", false) = true
    `;
    return this.ds.query(q);
  }

  private async alreadySentThisMonth(userId: string, type: 'MONTHLY_LIMIT'|'THRESHOLD_80'): Promise<boolean> {
    const q = `
      select 1
      from "${this.schema}"."notifications" n
      where n."type" = $1
        and date_trunc('month', n."sent_at") = date_trunc('month', now())
      limit 1
    `;
    const rows = await this.ds.query(q, [type]);
    return rows.length > 0;
  }

  @Cron('0 6 * * *', { timeZone: TZ }) 
  async monthlySummaryJob() {
    const today = new Date();
    const day = today.getDate();
    const year = today.getFullYear();

    const users = await this.getNotifiableUsers();
    for (const u of users) {
      const reminderDay = u.monthlyReminderDay ?? 1;
      if (day !== reminderDay) continue; 

      try {
        const payload = await this.notifications.dryRun(u.id, year, 'EMAIL');
        await this.notifications.send(u.id, year, payload.channel);
        this.logger.log(`Monthly summary stored for user=${u.id} year=${year}`);
      } catch (e) {
        this.logger.error(`Monthly summary failed for user=${u.id}: ${e.message}`);
      }
    }
  }

  @Cron(CronExpression.EVERY_DAY_AT_7AM, { timeZone: TZ })
  async thresholdDailyJob() {
    const year = new Date().getFullYear();
    const users = await this.getNotifiableUsers();

    for (const u of users) {
      try {
        const { hit80 } = await this.notifications.computeThresholds(u.id, year);
        if (!hit80) continue;

        const dup = await this.alreadySentThisMonth(u.id, 'THRESHOLD_80');
        if (dup) continue;

        await this.notifications.send(u.id, year, 'EMAIL'); 
        this.logger.log(`80% threshold stored for user=${u.id} year=${year}`);
      } catch (e) {
        this.logger.error(`Threshold job failed for user=${u.id}: ${e.message}`);
      }
    }
  }

  @Timeout(3000)
  async manualTestOnBoot() {
    if (process.env.NOTIF_MANUAL_TEST !== 'true') return;

    const userId = process.env.NOTIF_TEST_USER_ID;     // obrigatório
    const year = Number(process.env.NOTIF_TEST_YEAR) || new Date().getFullYear();
    const channel = (process.env.NOTIF_TEST_CHANNEL as 'SMS' | 'EMAIL') || 'EMAIL';

    if (!userId) {
      this.logger.warn('NOTIF_MANUAL_TEST=true, mas NOTIF_TEST_USER_ID não definido. Abortando teste manual.');
      return;
    }

    try {
      const res: any = await this.notifications.send(userId, year, channel);
      this.logger.log(`Manual test sent: user=${userId} year=${year} channel=${channel} id=${res.payload}`);
    } catch (error: any) {
      this.logger.error(`Manual test failed: ${error?.message || error}`);
    }
  }
}
