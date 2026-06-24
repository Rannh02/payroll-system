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
        Schema::create('position', function (Blueprint $table) {
            $table->id('position_id');
            $table->foreignId('department_id')->nullable()->constrained('department', 'department_id')->nullOnDelete();
            $table->string('position_name');
            $table->string('position_code')->unique();
            $table->string('description')->nullable();
            $table->decimal('basic_salary', 15, 2)->default(0);
            $table->enum('status', ['Active', 'Inactive'])->default('Active');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('position');
    }
};
