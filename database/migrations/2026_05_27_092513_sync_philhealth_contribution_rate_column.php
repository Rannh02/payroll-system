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
        Schema::table('philhealth', function (Blueprint $table) {
            if (Schema::hasColumn('philhealth', 'total_contribution') && !Schema::hasColumn('philhealth', 'contribution_rate')) {
                $table->renameColumn('total_contribution', 'contribution_rate');
            } elseif (!Schema::hasColumn('philhealth', 'contribution_rate')) {
                $table->decimal('contribution_rate', 5, 2)->after('salary_to');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('philhealth', function (Blueprint $table) {
            if (Schema::hasColumn('philhealth', 'contribution_rate') && !Schema::hasColumn('philhealth', 'total_contribution')) {
                $table->renameColumn('contribution_rate', 'total_contribution');
            }
        });
    }
};
