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
        Schema::create('branches', function (Blueprint $table) {
            $table->char('branch_no', 4)->primary();
            $table->string('street', 60);
            $table->string('area', 40)->nullable();
            $table->string('city', 30);
            $table->string('postcode', 10);
            $table->string('tel_no', 20)->nullable();
            $table->string('fax_no', 20)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('branches');
    }
};
