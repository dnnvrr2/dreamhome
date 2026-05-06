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
        Schema::create('managers', function (Blueprint $table) {
            $table->char('staff_no', 5)->primary();
            $table->date('date_start')->nullable();
            $table->decimal('car_allowance', 8, 2)->nullable();
            $table->decimal('bonus', 8, 2)->nullable();
            $table->foreign('staff_no')->references('staff_no')->on('staff')->cascadeOnDelete();
            $table->timestamps();
        });
    }
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('managers');
    }
};
