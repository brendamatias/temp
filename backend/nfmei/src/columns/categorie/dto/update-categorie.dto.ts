import { PartialType } from '@nestjs/swagger';
import { CreateExpenseCategoryDto } from './create-categorie.dto';

export class UpdateExpenseCategoryDto extends PartialType(CreateExpenseCategoryDto) {}
