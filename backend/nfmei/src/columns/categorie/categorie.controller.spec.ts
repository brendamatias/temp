import { Test, TestingModule } from '@nestjs/testing';
import { ExpenseCategoriesController } from './categorie.controller';
import { ExpenseCategoriesService } from './categorie.service';

const categoriesServiceMock = {};

describe('ExpenseCategoriesController', () => {
  let controller: ExpenseCategoriesController;

  beforeEach(async () => {
    const module: TestingModule = await Test.createTestingModule({
      controllers: [ExpenseCategoriesController],
      providers: [{ provide: ExpenseCategoriesService, useValue: categoriesServiceMock }],
    }).compile();

    controller = module.get<ExpenseCategoriesController>(ExpenseCategoriesController);
    jest.clearAllMocks();
  });

  it('should be defined', () => {
    expect(controller).toBeDefined();
  });
});
