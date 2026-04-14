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
        Schema::table('rx_details', function (Blueprint $table) {
            $table->integer('noveltreat_count')->default(0)->after('rx_count');
            $table->integer('sematrinity_count')->default(0)->after('noveltreat_count');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('rx_details', function (Blueprint $table) {
            $table->dropColumn(['noveltreat_count', 'sematrinity_count']);
        });
    }
};
