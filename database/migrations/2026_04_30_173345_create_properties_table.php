<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up() {
        Schema::create('properties', function (Blueprint $table) {
            $table->char('property_no', 5)->primary();
            $table->string('street', 60);
            $table->string('area', 40)->nullable();
            $table->string('city', 30);
            $table->string('postcode', 10)->nullable();
            $table->string('type', 20)->nullable();
            $table->smallInteger('rooms')->nullable();
            $table->decimal('rent', 8, 2)->nullable();
            $table->boolean('is_available')->default(true);
            $table->char('owner_no', 5)->nullable();
            $table->char('branch_no', 4)->nullable();
            $table->foreign('owner_no')->references('owner_no')->on('owners')->nullOnDelete();
            $table->foreign('branch_no')->references('branch_no')->on('branches')->nullOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('properties');
    }
};
