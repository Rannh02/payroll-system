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
        Schema::create('attendance', function (Blueprint $table) {
            $table->id('attendance_id');
            $table->date('date');
            $table->time('time_in');
            $table->time('time_out');
            $table->decimal('overtime_hours', 15, 2);
            $table->decimal('total_hours', 15, 2);
            $table->enum('status', ['Present', 'Absent', 'Late', 'Undertime'])->default('Present');
            $table->integer('late_minutes')->default(0);
            $table->integer('undertime_minutes')->default(0);
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
        Schema::dropIfExists('attendance');
    }
};
