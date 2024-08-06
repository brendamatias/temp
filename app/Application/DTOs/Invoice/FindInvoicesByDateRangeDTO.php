<?php

namespace App\Application\DTOs\Invoice;

use DateTime;

class FindInvoicesByDateRangeDTO
{
    public function __construct(
        public readonly DateTime $startDate,
        public readonly DateTime $endDate
    ) {}
} 