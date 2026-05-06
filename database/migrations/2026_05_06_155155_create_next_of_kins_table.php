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
        Schema::create('next_of_kins', function (Blueprint $table) {
            $table->char('staff_no', 5)->primary();
            $table->string('full_name', 60);
            $table->string('relationship', 30)->nullable();
            $table->string('street', 60)->nullable();
            $table->string('city', 30)->nullable();
            $table->string('tel_no', 20)->nullable();
            $table->foreign('staff_no')->references('staff_no')->on('staff')->cascadeOnDelete();
            $table->timestamps();
        });
    }
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('next_of_kins');
    }
};
