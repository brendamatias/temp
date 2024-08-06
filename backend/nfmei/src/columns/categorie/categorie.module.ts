import { Module } from '@nestjs/common';
import { TypeOrmModule } from '@nestjs/typeorm';
import { ExpenseCategory } from './entities/categorie.entity';
import { ExpenseCategoriesService } from './categorie.service';
import { ExpenseCategoriesController } from './categorie.controller';
import { Expense } from './entities/expense.entity';


@Module({
  imports: [TypeOrmModule.forFeature([ExpenseCategory, Expense])],
  controllers: [ExpenseCategoriesController],
  providers: [ExpenseCategoriesService],
  exports: [ExpenseCategoriesService],
})
export class ExpenseCategoriesModule {}