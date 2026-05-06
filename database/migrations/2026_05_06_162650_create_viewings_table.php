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
        Schema::create('viewings', function (Blueprint $table) {
            $table->char('client_no', 5);
            $table->char('property_no', 5);
            $table->date('view_date');
            $table->char('staff_no', 5)->nullable();
            $table->text('comments')->nullable();
            $table->foreign('client_no')->references('client_no')->on('clients')->cascadeOnDelete();
            $table->foreign('property_no')->references('property_no')->on('properties')->cascadeOnDelete();
            $table->foreign('staff_no')->references('staff_no')->on('staff')->nullOnDelete();
            $table->primary(['client_no', 'property_no', 'view_date']);
            $table->timestamps();
        });
    }
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('viewings');
    }
};
