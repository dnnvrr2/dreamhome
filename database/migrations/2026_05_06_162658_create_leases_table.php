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
        Schema::create('leases', function (Blueprint $table) {
            $table->char('lease_no', 5)->primary();
            $table->decimal('monthly_rent', 8, 2)->nullable();
            $table->string('payment_method', 40)->nullable();
            $table->decimal('deposit', 8, 2)->nullable();
            $table->boolean('deposit_paid')->default(false);
            $table->date('date_start')->nullable();
            $table->date('date_end')->nullable();
            $table->smallInteger('duration_month')->nullable();
            $table->char('client_no', 5)->nullable();
            $table->char('property_no', 5)->nullable();
            $table->char('staff_no', 5)->nullable();
            $table->foreign('client_no')->references('client_no')->on('clients')->nullOnDelete();
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
        Schema::dropIfExists('leases');
    }
};
