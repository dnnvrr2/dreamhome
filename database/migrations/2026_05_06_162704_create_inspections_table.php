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
        Schema::create('inspections', function (Blueprint $table) {
            $table->increments('inspection_id');
            $table->char('property_no', 5)->nullable();
            $table->char('staff_no', 5)->nullable();
            $table->date('inspection_date');
            $table->text('comments')->nullable();
            $table->foreign('property_no')->references('property_no')->on('properties')->nullOnDelete();
            $table->foreign('staff_no')->references('staff_no')->on('staff')->nullOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inspections');
    }
};
