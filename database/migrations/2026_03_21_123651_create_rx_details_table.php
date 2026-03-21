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
        Schema::create('rx_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('zone_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('region_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('hq_id')->nullable()->constrained()->onDelete('set null');
            $table->integer('rx_count')->default(0);
            $table->date('date')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rx_details');
    }
};
