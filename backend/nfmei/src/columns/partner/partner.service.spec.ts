import { Test, TestingModule } from '@nestjs/testing';
import { getRepositoryToken } from '@nestjs/typeorm';
import { PartnerService } from './partner.service';
import { Partner } from './entities/partner.entity';
import { DataSource } from 'typeorm';

const makeRepoMock = () => ({});

describe('PartnerService', () => {
  let service: PartnerService;

  beforeEach(async () => {
    const module: TestingModule = await Test.createTestingModule({
      providers: [
        PartnerService,
        { provide: getRepositoryToken(Partner), useValue: makeRepoMock() },
        { provide: DataSource, useValue: { query: jest.fn(), manager: {}, createQueryRunner: jest.fn() } },
      ],
    }).compile();

    service = module.get<PartnerService>(PartnerService);
    jest.clearAllMocks();
  });

  it('should be defined', () => {
    expect(service).toBeDefined();
  });
});
