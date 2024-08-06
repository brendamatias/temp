import { Test, TestingModule } from '@nestjs/testing';
import { getRepositoryToken } from '@nestjs/typeorm';
import { SettingsService } from './settings.service';
import { Setting } from './entities/setting.entity';
import { DataSource } from 'typeorm'; // inclua só se o service usar

const makeRepoMock = () => ({});

describe('SettingsService', () => {
  let service: SettingsService;

  beforeEach(async () => {
    const module: TestingModule = await Test.createTestingModule({
      providers: [
        SettingsService,
        { provide: getRepositoryToken(Setting), useValue: makeRepoMock() },
        { provide: DataSource, useValue: { query: jest.fn(), manager: {}, createQueryRunner: jest.fn() } },
      ],
    }).compile();

    service = module.get<SettingsService>(SettingsService);
    jest.clearAllMocks();
  });

  it('should be defined', () => {
    expect(service).toBeDefined();
  });
});
