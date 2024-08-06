import { NotFoundException } from '@nestjs/common';
import { DataSource } from 'typeorm';
import { NotificationsService } from './notifications.service';

jest.mock('src/utils/compute-notifications-thresholds.utils');

const mockRepo = () => ({
  create: jest.fn(),
  save: jest.fn(),
  find: jest.fn(),
  findOne: jest.fn(),
  preload: jest.fn(),
  remove: jest.fn(),
});

describe('NotificationsService', () => {
  let service: NotificationsService;
  let repo: ReturnType<typeof mockRepo>;
  let ds: DataSource;

  beforeEach(() => {
    repo = mockRepo();
    ds = { query: jest.fn() } as any;
    service = new NotificationsService(repo as any, ds);
    jest.clearAllMocks();
  });

  it('create() should create and save', async () => {
    const dto = { type: 'MONTHLY_LIMIT', channel: 'EMAIL', payload: { a: 1 } } as any;
    const entity = { id: 'n1', ...dto } as any;
    repo.create.mockReturnValue(entity);
    repo.save.mockResolvedValue(entity);

    const res = await service.create(dto);

    expect(repo.create).toHaveBeenCalledWith(expect.objectContaining(dto));
    expect(repo.save).toHaveBeenCalledWith(entity);
    expect(res).toBe(entity);
  });

  it('findAll() should apply channel/type/date filters and order by sentAt DESC', async () => {
    repo.find.mockResolvedValue(['x']);
    const params = { channel: 'SMS' as const, type: 'THRESHOLD_80', dateFrom: '2025-01-01', dateTo: '2025-12-31' };

    const res = await service.findAll(params);

    expect(res).toEqual(['x']);
    expect(repo.find).toHaveBeenCalledWith(
      expect.objectContaining({
        where: expect.objectContaining({
          channel: 'SMS',
          type: 'THRESHOLD_80',
          sentAt: expect.any(Object),
        }),
        order: { sentAt: 'DESC' },
      }),
    );
  });

  it('findAll() should build Between when only dateFrom is provided', async () => {
    repo.find.mockResolvedValue([]);
    await service.findAll({ dateFrom: '2025-03-01' });
    const arg = (repo.find as jest.Mock).mock.calls[0][0];
    expect(arg.where.sentAt).toBeDefined();
  });

  it('findOne() should return entity or throw NotFound', async () => {
    repo.findOne.mockResolvedValue({ id: '1' });
    await expect(service.findOne('1')).resolves.toEqual({ id: '1' });

    repo.findOne.mockResolvedValue(null);
    await expect(service.findOne('2')).rejects.toBeInstanceOf(NotFoundException);
  });

  it('update() should save when preload returns entity; else NotFound', async () => {
    const preload = { id: '1' } as any;
    const saved = { id: '1', updated: true } as any;

    repo.preload.mockResolvedValue(preload);
    repo.save.mockResolvedValue(saved);
    await expect(service.update('1', { payload: { x: 1 } } as any)).resolves.toBe(saved);

    repo.preload.mockResolvedValue(null);
    await expect(service.update('nope', {} as any)).rejects.toBeInstanceOf(NotFoundException);
  });
});
