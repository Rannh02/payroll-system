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
        Schema::create('employee', function (Blueprint $table) {
            $table->id('employee_id');
            
            // Foreign Keys
            $table->unsignedBigInteger('position_id')->nullable();
            $table->unsignedBigInteger('department_id')->nullable();
            $table->unsignedBigInteger('created_by')->nullable(); // FK_User_id
            $table->unsignedBigInteger('user_id')->nullable();
            
            $table->foreign('position_id')->references('position_id')->on('position')->onDelete('set null');
            $table->foreign('department_id')->references('department_id')->on('department')->onDelete('set null');
            $table->foreign('created_by')->references('id')->on('users')->onDelete('set null');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('set null');

            // Personal Information
            $table->string('first_name');
            $table->string('middle_name')->nullable();
            $table->string('last_name');
            $table->string('sex')->nullable();

            // Current Address Breakdown
            $table->string('current_street_address')->nullable();
            $table->string('current_barangay')->nullable();
            $table->string('current_city_municipality')->nullable();
            $table->string('current_province')->nullable();
            $table->string('current_zip_code')->nullable();

            // permanent address breakdown (old address)
            $table->string('permanent_street_address')->nullable();
            $table->string('permanent_barangay')->nullable();
            $table->string('permanent_city_municipality')->nullable();
            $table->string('permanent_province')->nullable();
            $table->string('permanent_zip_code')->nullable();

            // Other details
            $table->string('contact_info')->nullable();
            $table->date('date_of_birth')->nullable();
            
            $table->decimal('salary_rate', 15, 2)->nullable();
            $table->date('hire_date')->nullable();
            
            $table->string('employment_status')->nullable();
            $table->string('marital_status')->nullable();
            $table->integer('number_of_dependents')->default(0);
            $table->string('spouse')->nullable();
            $table->string('sss_num')->nullable();
            $table->string('philhealth_num')->nullable();
            $table->string('pagibig_num')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employee');
    }
};
