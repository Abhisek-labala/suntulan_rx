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
            // Drop old string columns
            $table->dropColumn(['designation', 'hq', 'region', 'zone']);
            
            // Add new foreign key columns
            $table->foreignId('designation_id')->nullable()->constrained('designations')->onDelete('set null');
            $table->foreignId('hq_id')->nullable()->constrained('hqs')->onDelete('set null');
            $table->foreignId('region_id')->nullable()->constrained('regions')->onDelete('set null');
            $table->foreignId('zone_id')->nullable()->constrained('zones')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['designation_id']);
            $table->dropForeign(['hq_id']);
            $table->dropForeign(['region_id']);
            $table->dropForeign(['zone_id']);
            $table->dropColumn(['designation_id', 'hq_id', 'region_id', 'zone_id']);
            
            $table->string('designation')->nullable();
            $table->string('hq')->nullable();
            $table->string('region')->nullable();
            $table->string('zone')->nullable();
        });
    }
};
