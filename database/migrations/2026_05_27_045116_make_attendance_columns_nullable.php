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
        Schema::table('attendance', function (Blueprint $table) {
            $table->time('time_in')->nullable()->change();
            $table->time('time_out')->nullable()->change();
            $table->string('status', 50)->default('Present')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('attendance', function (Blueprint $table) {
            $table->time('time_in')->nullable(false)->change();
            $table->time('time_out')->nullable(false)->change();
            $table->enum('status', ['Present', 'Absent', 'Late', 'Undertime'])->default('Present')->change();
        });
    }
};
