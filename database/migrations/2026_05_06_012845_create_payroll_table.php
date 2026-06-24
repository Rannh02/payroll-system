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
        Schema::create('payroll', function (Blueprint $table) {
            $table->id('payroll_id');
            //payroll date
            $table->date('payroll_period_start');
            $table->date('payroll_period_end');
            $table->date('payroll_date');
            
            //employee earned salary with deductions and benefits
            $table->decimal('basic_salary', 15, 2);
            $table->decimal('overtime_pay', 15, 2);
            $table->decimal('gross_pay', 15, 2);
            $table->decimal('total_deductions', 15, 2);
            $table->decimal('net_pay', 15, 2);

            $table->unsignedBigInteger('employee_id');
            $table->foreign('employee_id')->references('employee_id')->on('employee')->onDelete('cascade'); 
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payroll');
    }
};
