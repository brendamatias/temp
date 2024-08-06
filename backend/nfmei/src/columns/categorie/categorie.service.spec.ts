import { Test, TestingModule } from '@nestjs/testing';
import { getRepositoryToken } from '@nestjs/typeorm';

import { ExpenseCategoriesService } from './categorie.service';

import { ExpenseCategory } from './entities/categorie.entity'; // ajuste o caminho/nome do arquivo
import { Expense } from './entities/expense.entity';


const makeRepoMock = () => ({
  find: jest.fn(),
  findOne: jest.fn(),
  findOneBy: jest.fn(),
  create: jest.fn(),
  save: jest.fn(),
  update: jest.fn(),
  delete: jest.fn(),
  preload: jest.fn(),
});

describe('ExpenseCategoriesService', () => {
  let service: ExpenseCategoriesService;

  beforeEach(async () => {
    const module: TestingModule = await Test.createTestingModule({
      providers: [
        ExpenseCategoriesService,
        { provide: getRepositoryToken(ExpenseCategory), useValue: makeRepoMock() },
        { provide: getRepositoryToken(Expense), useValue: makeRepoMock() },
      ],
    }).compile();

    service = module.get<ExpenseCategoriesService>(ExpenseCategoriesService);
  });

  it('should be defined', () => {
    expect(service).toBeDefined();
  });

});