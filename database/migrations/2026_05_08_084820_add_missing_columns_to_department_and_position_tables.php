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
        // Add missing columns to department table
        Schema::table('department', function (Blueprint $table) {
            if (!Schema::hasColumn('department', 'department_code')) {
                $table->string('department_code', 20)->unique()->after('department_id');
            }
            if (!Schema::hasColumn('department', 'description')) {
                $table->string('description', 255)->nullable()->after('department_name');
            }
            if (!Schema::hasColumn('department', 'status')) {
                $table->enum('status', ['Active', 'Inactive'])->default('Active')->after('description');
            }
        });

        // Add missing columns to position table
        Schema::table('position', function (Blueprint $table) {
            if (!Schema::hasColumn('position', 'department_id')) {
                $table->unsignedBigInteger('department_id')->nullable()->after('position_id');
                $table->foreign('department_id')->references('department_id')->on('department')->nullOnDelete();
            }
            if (!Schema::hasColumn('position', 'position_name')) {
                $table->string('position_name', 100)->after('department_id');
            }
            if (!Schema::hasColumn('position', 'position_code')) {
                $table->string('position_code', 20)->unique()->after('position_name');
            }
            if (!Schema::hasColumn('position', 'description')) {
                $table->string('description', 255)->nullable()->after('basic_salary');
            }
            if (!Schema::hasColumn('position', 'status')) {
                $table->enum('status', ['Active', 'Inactive'])->default('Active')->after('description');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('position', function (Blueprint $table) {
            if (Schema::hasColumn('position', 'department_id')) {
                $table->dropForeign(['department_id']);
                $table->dropColumn('department_id');
            }
            if (Schema::hasColumn('position', 'position_code')) {
                $table->dropColumn('position_code');
            }
            if (Schema::hasColumn('position', 'description')) {
                $table->dropColumn('description');
            }
            if (Schema::hasColumn('position', 'status')) {
                $table->dropColumn('status');
            }
        });

        Schema::table('department', function (Blueprint $table) {
            if (Schema::hasColumn('department', 'department_code')) {
                $table->dropColumn('department_code');
            }
            if (Schema::hasColumn('department', 'description')) {
                $table->dropColumn('description');
            }
            if (Schema::hasColumn('department', 'status')) {
                $table->dropColumn('status');
            }
        });
    }
};
