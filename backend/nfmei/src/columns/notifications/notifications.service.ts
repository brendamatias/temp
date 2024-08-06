import { Injectable, NotFoundException } from '@nestjs/common';
import { InjectRepository } from '@nestjs/typeorm';
import { Between, DataSource, Repository } from 'typeorm';
import { Notification } from './entities/notification.entity';
import { CreateNotificationDto } from './dto/create-notification.dto';
import { UpdateNotificationDto } from './dto/update-notification.dto';
import { computeNotificationThresholds } from 'src/utils/compute-notifications-thresholds.utils'
import { ThresholdResult } from 'src/common/types/notification.types';

@Injectable()
export class NotificationsService {
  private readonly schema = process.env.DB_SCHEMA || 'public';

  constructor(
    @InjectRepository(Notification)
    private readonly repo: Repository<Notification>,
    private readonly ds: DataSource
  ) {}

  async create(dto: CreateNotificationDto): Promise<Notification[]> {
    const entity = this.repo.create({
      ...dto,
    } as any);
    return this.repo.save(entity);
  }

  async findAll(params?: {
    channel?: 'SMS' | 'EMAIL';
    dateFrom?: string; 
    dateTo?: string; 
    type?: string;
  }): Promise<Notification[]> {
    const where: any = {};
    if (params?.channel) where.channel = params.channel;
    if (params?.type) where.type = params.type;
    if (params?.dateFrom || params?.dateTo) {
      const from = params?.dateFrom ? new Date(params.dateFrom) : new Date('1970-01-01');
      const to = params?.dateTo ? new Date(params.dateTo) : new Date('2999-12-31');
      where.sentAt = Between(from, to);
    }
    return this.repo.find({ where, order: { sentAt: 'DESC' } });
  }

  async findOne(id: string): Promise<Notification> {
    const entity = await this.repo.findOne({ where: { id } });
    if (!entity) throw new NotFoundException(`Notification with ID "${id}" not found`);
    return entity;
  }

  async update(id: string, dto: UpdateNotificationDto): Promise<Notification> {
    const preload = await this.repo.preload({ id, ...(dto as any) });
    if (!preload) throw new NotFoundException(`Notification with ID "${id}" not found`);
    return this.repo.save(preload);
  }

  async remove(id: string): Promise<void> {
    const entity = await this.findOne(id);
    await this.repo.remove(entity);
  }

  async computeThresholds(userId: string, year: number): Promise<ThresholdResult> {
    const [used, limit] = await Promise.all([
      this.totalInvoicesOfYear(year),
      this.meiLimit(userId)
    ]);

    return computeNotificationThresholds(year, used, limit);
  }

  private async totalInvoicesOfYear(year: number): Promise<number> {
    const q = `
      select coalesce(sum(amount),0)::numeric as total
      from "${this.schema}"."invoices"
      where extract(year from reference_month) = $1
    `;
    const [row] = await this.ds.query(q, [year]);
    return Number(row?.total ?? 0);
  }

  private async meiLimit(userId: string): Promise<number> {
    const q = `
      select coalesce("mei_annual_limit", 81000)::numeric as lim
      from "${this.schema}"."settings" where user_id = $1 limit 1
    `;
    const [row] = await this.ds.query(q, [userId]);
    return Number(row?.lim ?? 81000);
  }

  async dryRun(userId: string, year: number, channel: 'SMS' | 'EMAIL') {
    const { hit80, payload } = await this.computeThresholds(userId, year);
    const type = hit80 ? 'THRESHOLD_80' : 'MONTHLY_LIMIT';
    return { type, channel, payload };
  }

  async send(userId: string, year: number, channel: 'SMS' | 'EMAIL') {
    const { hit80, payload } = await this.computeThresholds(userId, year);
    const type = hit80 ? 'THRESHOLD_80' : 'MONTHLY_LIMIT';

    const saved = await this.create({ type, channel, payload } as any);
    return saved;
  }
}
