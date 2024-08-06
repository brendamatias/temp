<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('expenses', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->decimal('value', 10, 2);
            $table->date('payment_date');
            $table->date('competence_date');
            $table->foreignId('category_id')->constrained('expense_categories');
            $table->foreignId('partner_company_id')->nullable()->constrained('partner_companies');
            $table->foreignId('invoice_id')->nullable()->constrained('invoices');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('expenses');
    }
}; 