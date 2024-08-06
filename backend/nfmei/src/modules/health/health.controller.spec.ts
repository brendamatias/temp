import { Test, TestingModule } from '@nestjs/testing';
import { HealthController } from './health.controller';
import { ConfigService } from '@nestjs/config';

const configMock = {};

describe('HealthController', () => {
  let controller: HealthController;

  beforeEach(async () => {
    const module: TestingModule = await Test.createTestingModule({
      controllers: [HealthController],
      providers: [{ provide: ConfigService, useValue: configMock }],
    }).compile();

    controller = module.get<HealthController>(HealthController);
    jest.clearAllMocks();
  });

  it('should be defined', () => {
    expect(controller).toBeDefined();
  });
});
