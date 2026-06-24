<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('login_logs', function (Blueprint $table) {
            $table->string('browser')->nullable()->after('user_agent');
            $table->timestamp('locked_until')->nullable()->after('browser');
        });
    }

    public function down(): void
    {
        Schema::table('login_logs', function (Blueprint $table) {
            $table->dropColumn(['browser', 'locked_until']);
        });
    }
};
