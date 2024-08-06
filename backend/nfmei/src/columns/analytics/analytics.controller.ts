import { Controller, Get, Param, ParseIntPipe, Query, Req, UseGuards } from '@nestjs/common';
import { ApiBearerAuth, ApiOkResponse, ApiOperation, ApiQuery, ApiTags } from '@nestjs/swagger';
import { AnalyticsService } from './analytics.service';
import { JwtAuthGuard } from 'src/common/guards/jwt-auth.guard';


@ApiTags('analytics')
@ApiBearerAuth()
@UseGuards(JwtAuthGuard)
@Controller('analytics')
export class AnalyticsController {
  constructor(private readonly service: AnalyticsService) {}

  @Get(':year/progress')
  @ApiOperation({ summary: 'MEI revenue progress (used vs remaining) for a year' })
  @ApiOkResponse({ description: 'Progress returned.' })
  getProgress(
    @Param('year', ParseIntPipe) year: number,
    @Req() req: any,
  ) {
    return this.service.getMeiProgress(year, String(req.user?.userId ?? '0'));
  }

  @Get(':year/invoices')
  @ApiOperation({ summary: 'Monthly invoices total for a year' })
  @ApiOkResponse({ description: 'Monthly totals returned.' })
  getMonthlyInvoices(@Param('year', ParseIntPipe) year: number) {
    return this.service.getMonthlyInvoices(year);
  }

  @Get(':year/expenses')
  @ApiOperation({ summary: 'Monthly expenses total for a year' })
  @ApiOkResponse({ description: 'Monthly totals returned.' })
  getMonthlyExpenses(@Param('year', ParseIntPipe) year: number) {
    return this.service.getMonthlyExpenses(year);
  }

  @Get(':year/balance')
  @ApiOperation({ summary: 'Monthly balance (invoices - expenses) for a year' })
  @ApiOkResponse({ description: 'Monthly balance returned.' })
  getMonthlyBalance(@Param('year', ParseIntPipe) year: number) {
    return this.service.getMonthlyBalance(year);
  }

  @Get(':year/expenses-by-category')
  @ApiOperation({ summary: 'Expenses by category for a given year and month' })
  @ApiQuery({ name: 'month', required: false, description: 'Month (1..12). Defaults to current month.' })
  @ApiOkResponse({ description: 'Totals by category returned.' })
  getExpensesByCategory(
    @Param('year', ParseIntPipe) year: number,
    @Query('month') month?: string,
  ) {
    const m = month ? parseInt(month, 10) : undefined;
    return this.service.getExpensesByCategory(year, m);
  }
}
