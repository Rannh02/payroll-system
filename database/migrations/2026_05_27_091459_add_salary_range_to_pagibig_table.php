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
        Schema::table('pagibig', function (Blueprint $table) {
            if (!Schema::hasColumn('pagibig', 'salary_from')) {
                $table->decimal('salary_from', 10, 2)->after('pagibig_id');
            }
            if (!Schema::hasColumn('pagibig', 'salary_to')) {
                $table->decimal('salary_to', 10, 2)->after('salary_from');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pagibig', function (Blueprint $table) {
            if (Schema::hasColumn('pagibig', 'salary_from')) {
                $table->dropColumn('salary_from');
            }
            if (Schema::hasColumn('pagibig', 'salary_to')) {
                $table->dropColumn('salary_to');
            }
        });
    }
};
