<?php

use App\Http\Controllers\SuperAdminController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\ValidationException;

describe('superadmin admin password validation', function () {
    it('rejects passwords that do not meet the required complexity rules', function () {
        Schema::create('admin', function ($table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->string('password');
            $table->string('role')->default('admin');
            $table->rememberToken();
            $table->timestamps();
        });

        $request = Request::create('/superadmin/Administrator', 'POST', [
            'name' => 'Admin User',
            'email' => 'admin-password-test@example.com',
            'password' => 'Password1',
            'password_confirmation' => 'Password1',
        ]);

        $controller = new SuperAdminController();

        expect(fn () => $controller->storeAdmin($request))
            ->toThrow(ValidationException::class);
    });
});
