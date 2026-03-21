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
        Schema::table('users', function (Blueprint $table) {
            $table->string('role')->default('sales_team'); // admin or sales_team
            $table->string('username')->unique()->nullable();
            $table->string('employee_id')->unique()->nullable();
            $table->string('prefix')->nullable();
            $table->string('designation')->nullable();
            $table->string('hq')->nullable();
            $table->string('region')->nullable();
            $table->string('zone')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['role', 'prefix', 'designation', 'hq', 'region', 'zone']);
        });
    }
};
