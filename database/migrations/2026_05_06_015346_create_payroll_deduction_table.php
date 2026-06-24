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
        Schema::create('payroll_deduction', function (Blueprint $table) {
            $table->id('payroll_deduction_id');
            $table->unsignedBigInteger('payroll_id');
            $table->foreign('payroll_id')->references('payroll_id')->on('payroll')->onDelete('cascade');
            $table->unsignedBigInteger('deduction_id');
            $table->foreign('deduction_id')->references('deduction_id')->on('deduction')->onDelete('cascade');
            $table->decimal('deduction_amount', 15, 2);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payroll_deduction');
    }
};
