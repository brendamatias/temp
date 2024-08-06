<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->string('number');
            $table->decimal('value', 10, 2);
            $table->text('service_description');
            $table->date('competence_month');
            $table->date('receipt_date');
            $table->foreignId('partner_company_id')->constrained('partner_companies');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['number', 'partner_company_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('invoices');
    }
}; 