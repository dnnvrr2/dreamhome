<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('staff', function (Blueprint $table) {
            $table->char('staff_no', 5)->primary();
            $table->string('f_name', 30);
            $table->string('l_name', 30);
            $table->string('street', 60)->nullable();
            $table->string('area', 30)->nullable();
            $table->string('city', 30)->nullable();
            $table->string('postcode', 10)->nullable();
            $table->string('tel_no', 20)->nullable();
            $table->char('sex', 1)->nullable();
            $table->date('dob')->nullable();
            $table->string('nin', 12)->nullable();
            $table->string('position', 20);
            $table->decimal('salary', 9, 2)->nullable();
            $table->date('date_joined')->nullable();
            $table->char('branch_no', 4)->nullable();
            $table->char('supervisor_no', 5)->nullable();
            $table->foreign('branch_no')->references('branch_no')->on('branches')->nullOnDelete();
            $table->timestamps();
        });

        // Add self-referencing FK after table exists
        Schema::table('staff', function (Blueprint $table) {
            $table->foreign('supervisor_no')->references('staff_no')->on('staff')->nullOnDelete();
        });
    }

    public function down(): void {
        Schema::dropIfExists('staff');
    }
};