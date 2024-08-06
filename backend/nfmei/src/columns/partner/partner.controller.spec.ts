import { Test, TestingModule } from '@nestjs/testing';
import { PartnerController } from './partner.controller';
import { PartnerService } from './partner.service';

const partnerServiceMock = {};

describe('PartnerController', () => {
  let controller: PartnerController;

  beforeEach(async () => {
    const module: TestingModule = await Test.createTestingModule({
      controllers: [PartnerController],
      providers: [
        { provide: PartnerService, useValue: partnerServiceMock },
      ],
    }).compile();

    controller = module.get<PartnerController>(PartnerController);
    jest.clearAllMocks();
  });

  it('should be defined', () => {
    expect(controller).toBeDefined();
  });
});
