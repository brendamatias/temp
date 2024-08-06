import { Test, TestingModule } from '@nestjs/testing';
import { getRepositoryToken } from '@nestjs/typeorm';
import { InvoicesService } from './invoices.service';
import { Invoice } from './entities/invoice.entity';
import { Partner } from 'src/columns/partner/entities/partner.entity';
import { DataSource } from 'typeorm';

const makeRepoMock = () => ({});

describe('InvoicesService', () => {
  let service: InvoicesService;

  beforeEach(async () => {
    const module: TestingModule = await Test.createTestingModule({
      providers: [
        InvoicesService,

        { provide: getRepositoryToken(Invoice), useValue: makeRepoMock() },
        { provide: getRepositoryToken(Partner), useValue: makeRepoMock() },
        { provide: DataSource, useValue: { query: jest.fn(), manager: {}, createQueryRunner: jest.fn() } },
      ],
    }).compile();

    service = module.get<InvoicesService>(InvoicesService);
    jest.clearAllMocks();
  });

  it('should be defined', () => {
    expect(service).toBeDefined();
  });
});
