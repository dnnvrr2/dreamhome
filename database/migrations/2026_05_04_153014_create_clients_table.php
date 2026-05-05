<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('clients', function (Blueprint $table) {
            $table->char('client_no', 5)->primary();
            $table->string('f_name', 30);
            $table->string('l_name', 40);
            $table->string('street', 60)->nullable();
            $table->string('area', 30)->nullable();
            $table->string('city', 30)->nullable();
            $table->string('postcode', 10)->nullable();
            $table->string('tel_no', 20)->nullable();
            $table->string('pref_type', 20)->nullable();
            $table->decimal('max_rent', 8, 2)->nullable();
            $table->text('comments')->nullable();
            $table->char('registered_by', 5)->nullable();
            $table->char('branch_no', 4)->nullable();
            $table->foreign('branch_no')->references('branch_no')->on('branches');
            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('clients');
    }
};