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
        Schema::create('tax', function (Blueprint $table) {
            $table->id('tax_id')->autoIncrement();

            $table->decimal('salary_from', 10, 2);
            $table->decimal('salary_to', 10, 2);
            $table->decimal('base_tax', 10, 2)->default(0);
            $table->decimal('tax_rate', 5, 2);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tax');
    }
};
