<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('client_requests', function (Blueprint $table) {
            $table->id();
            $table->char('property_no', 5)->nullable();
            $table->string('f_name', 30);
            $table->string('l_name', 40);
            $table->string('email', 120)->nullable();
            $table->string('tel_no', 20);
            $table->string('street', 60)->nullable();
            $table->string('area', 30)->nullable();
            $table->string('city', 30)->nullable();
            $table->string('postcode', 10)->nullable();
            $table->string('pref_type', 20)->nullable();
            $table->decimal('max_rent', 8, 2)->nullable();
            $table->date('preferred_view_date')->nullable();
            $table->text('comments')->nullable();
            $table->string('status', 20)->default('pending');
            $table->char('approved_client_no', 5)->nullable();
            $table->unsignedBigInteger('processed_by')->nullable();
            $table->timestamp('processed_at')->nullable();
            $table->timestamps();

            $table->foreign('property_no')->references('property_no')->on('properties')->nullOnDelete();
            $table->foreign('approved_client_no')->references('client_no')->on('clients')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('client_requests');
    }
};
