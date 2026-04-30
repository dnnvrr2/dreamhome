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
        Schema::create('owners', function (Blueprint $table) {
            $table->char('owner_no', 5)->primary();
            $table->string('f_name', 30);
            $table->string('l_name', 30);
            $table->string('street', 60)->nullable();
            $table->string('city', 30)->nullable();
            $table->string('postcode', 10)->nullable();
            $table->string('tel_no', 20)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('owners');
    }
};
