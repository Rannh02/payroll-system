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
        Schema::create('pagibig', function (Blueprint $table) {
            $table->id('pagibig_id')->autoIncrement();
            $table->decimal('salary_from', 10, 2);
            $table->decimal('salary_to', 10, 2);

            $table->decimal('employee_rate', 5, 2);
            $table->decimal('employer_rate', 5, 2);
            $table->decimal('maximum_contribution', 10, 2)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pagibig');
    }
};
