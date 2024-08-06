import { Injectable, NotFoundException } from '@nestjs/common';
import { InjectRepository } from '@nestjs/typeorm';
import { Repository } from 'typeorm';
import { ExpenseCategory } from './entities/categorie.entity';
import { CreateExpenseCategoryDto } from './dto/create-categorie.dto';
import { UpdateExpenseCategoryDto } from './dto/update-categorie.dto';
import { CreateExpenseDto } from './dto/create-expense.dto';
import { Expense } from './entities/expense.entity';
import { UpdateExpenseDto } from './dto/update-expense.dto';


@Injectable()
export class ExpenseCategoriesService {
  constructor(
    @InjectRepository(ExpenseCategory)
    private readonly catRepo: Repository<ExpenseCategory>,
    @InjectRepository(Expense)
    private readonly expRepo: Repository<Expense>,
  ) {}

  async create(dto: CreateExpenseCategoryDto): Promise<ExpenseCategory> {
    const entity = this.catRepo.create(dto);
    return this.catRepo.save(entity);
  }

  async findAll(): Promise<ExpenseCategory[]> {
    return this.catRepo.find();
  }

  async findOne(id: string): Promise<ExpenseCategory | null> {
    console.log('ID:::::::::::::::::::', id)
    const entity = await this.catRepo.findOne({ where: { id } });
    if (!entity) throw new NotFoundException(`ExpenseCategory with ID "${id}" not found`);
    return entity;
  }

  async update(id: string, dto: UpdateExpenseCategoryDto): Promise<ExpenseCategory> {
    const preload = await this.catRepo.preload({ id, ...dto });
    if (!preload) throw new NotFoundException(`ExpenseCategory with ID "${id}" not found`);
    return this.catRepo.save(preload);
  }

  async remove(id: string): Promise<void> {
    const entity = await this.findOne(id);
    // await this.catRepo.remove(entity);
  }

   async createExpense(dto: CreateExpenseDto): Promise<Expense> {
    await this.ensureCategory(dto.cathegoryId);
    const entity = this.expRepo.create(dto);
    return this.expRepo.save(entity);
  }

  async findAllExpenses(): Promise<Expense[]> {
    return this.expRepo.find({ relations: ['cathegory', 'partner'] });
  }

  async findExpense(id: string): Promise<Expense> {
    const entity = await this.expRepo.findOne({ where: { id }, relations: ['cathegory', 'partner'] });
    if (!entity) throw new NotFoundException(`Expense with ID "${id}" not found`);
    return entity;
  }

  async updateExpense(id: string, dto: UpdateExpenseDto): Promise<Expense> {
    if (dto.cathegoryId) await this.ensureCategory(dto.cathegoryId);
    const preload = await this.expRepo.preload({ id, ...dto });
    if (!preload) throw new NotFoundException(`Expense with ID "${id}" not found`);
    return this.expRepo.save(preload);
  }

  async removeExpense(id: string): Promise<void> {
    const entity = await this.findExpense(id);
    await this.expRepo.remove(entity);
  }

  async findExpensesByCategory(categoryId: string): Promise<Expense[]> {
    const ensure = await this.ensureCategory(categoryId);
    return this.expRepo.find({
      where: { cathegoryId: categoryId },
      relations: ['cathegory', 'partner'],
    });
  }

  async createExpenseInCategory(categoryId: string, dto: Omit<CreateExpenseDto, 'cathegoryId'>): Promise<Expense> {
    await this.ensureCategory(categoryId);
    const entity = this.expRepo.create({ ...dto, cathegoryId: categoryId });
    return this.expRepo.save(entity);
  }

  private async ensureCategory(id: string): Promise<void> {
    const exists = await this.catRepo.exists({ where: { id } });
    if (!exists) throw new NotFoundException(`ExpenseCategory with ID "${id}" not found`);
  }
}
