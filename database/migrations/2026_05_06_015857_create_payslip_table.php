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
        Schema::create('payslip', function (Blueprint $table) {
            $table->id('payslip_id');
            $table->unsignedBigInteger('payroll_id');
            $table->foreign('payroll_id')->references('payroll_id')->on('payroll')->onDelete('cascade');
            $table->date('issue_date');
            $table->date('pay_period_start');
            $table->date('pay_period_end');
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payslip');
    }
};
