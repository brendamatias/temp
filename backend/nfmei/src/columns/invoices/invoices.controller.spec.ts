import { Test, TestingModule } from '@nestjs/testing';
import { InvoicesController } from './invoices.controller';
import { InvoicesService } from './invoices.service';

const invoicesServiceMock = {};

describe('InvoicesController', () => {
  let controller: InvoicesController;

  beforeEach(async () => {
    const module: TestingModule = await Test.createTestingModule({
      controllers: [InvoicesController],
      providers: [{ provide: InvoicesService, useValue: invoicesServiceMock }],
    }).compile();

    controller = module.get<InvoicesController>(InvoicesController);
    jest.clearAllMocks();
  });

  it('should be defined', () => {
    expect(controller).toBeDefined();
  });
});