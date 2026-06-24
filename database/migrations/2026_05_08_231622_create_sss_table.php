<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('sss', function (Blueprint $table) {
            $table->id('sss_id')->autoIncrement();

            $table->decimal('sss_range_from', 10, 2);
            $table->decimal('sss_range_to', 10, 2);
            $table->decimal('monthly_salary_credit', 10, 2);
            $table->decimal('employee_share', 10, 2);
            $table->decimal('employer_share', 10, 2);
            $table->decimal('total_contribution', 10, 2);
            

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sss');
    }
};
