import { Controller, Get, Post, Body, Patch, Param, Delete, UseGuards, Query } from '@nestjs/common';
import { ApiTags, ApiBearerAuth, ApiOkResponse, ApiNoContentResponse } from '@nestjs/swagger';

import { JwtAuthGuard } from 'src/common/guards/jwt-auth.guard';
import { ExpenseCategoriesService } from './categorie.service';
import { CreateExpenseCategoryDto } from './dto/create-categorie.dto';
import { UpdateExpenseCategoryDto } from './dto/update-categorie.dto';
import { CreateExpenseDto } from './dto/create-expense.dto';
import { UpdateExpenseDto } from './dto/update-expense.dto';

@ApiTags('categories')
@ApiBearerAuth()
@UseGuards(JwtAuthGuard)
@Controller('categories')
export class ExpenseCategoriesController {
  constructor(private readonly service: ExpenseCategoriesService) {}

  @Post()
  @ApiOkResponse({ type: CreateExpenseCategoryDto })
  create(@Body() dto: CreateExpenseCategoryDto) {
    return this.service.create(dto);
  }

  @Get()
  @ApiOkResponse({ description: 'Lista de categorias retornada com sucesso.' })
  findAll() {
    return this.service.findAll();
  }

  @Post('/expenses')
  @ApiOkResponse({ type: CreateExpenseDto })
  createExpense(@Body() dto: CreateExpenseDto) {
    return this.service.createExpense(dto);
  }

  @Get('/expenses')
  @ApiOkResponse({ description: 'Lista de despesas retornada com sucesso.' })
  findAllExpenses(@Query('categoryId') categoryId?: string) {
    if (categoryId) return this.service.findExpensesByCategory(categoryId);
    return this.service.findAllExpenses();
  }

  @Get('/expenses/:id')
  @ApiOkResponse({ description: 'Despesa encontrada.' })
  findExpense(@Param('id') id: string) {
    return this.service.findExpense(id);
  }

  @Patch('/expenses/:id')
  @ApiOkResponse({ description: 'Despesa atualizada com sucesso.' })
  updateExpense(@Param('id') id: string, @Body() dto: UpdateExpenseDto) {
    return this.service.updateExpense(id, dto);
  }

  @Delete('/expenses/:id')
  @ApiNoContentResponse({ description: 'Despesa removida com sucesso.' })
  removeExpense(@Param('id') id: string): Promise<void> {
    return this.service.removeExpense(id);
  }

  @Post(':categoryId/expenses')
  createExpenseInCategory(
    @Param('categoryId') categoryId: string,
    @Body() dto: Omit<CreateExpenseDto, 'cathegoryId'>,
  ) {
    return this.service.createExpenseInCategory(categoryId, dto);
  }

  @Get(':categoryId/expenses')
  listExpensesByCategory(@Param('categoryId') categoryId: string) {
    return this.service.findExpensesByCategory(categoryId);
  }


  @Get('/:id')
  @ApiOkResponse({ description: 'Categoria encontrada.' })
  findOne(@Param('id') id: string) {
    return this.service.findOne(id);
  }

  @Patch(':id')
  @ApiOkResponse({ description: 'Categoria atualizada com sucesso.' })
  update(@Param('id') id: string, @Body() dto: UpdateExpenseCategoryDto) {
    return this.service.update(id, dto);
  }

  @Delete(':id')
  @ApiNoContentResponse({ description: 'Categoria removida com sucesso.' })
  remove(@Param('id') id: string) {
    return this.service.remove(id);
  }
}
