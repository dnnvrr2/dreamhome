<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('client_requests', function (Blueprint $table) {
            $table->index('status', 'idx_client_requests_status');
            $table->index('property_no', 'idx_client_requests_property');
        });

        Schema::table('viewings', function (Blueprint $table) {
            $table->index('property_no', 'idx_viewings_property');
            $table->index('staff_no', 'idx_viewings_staff');
        });
    }

    public function down(): void
    {
        Schema::table('viewings', function (Blueprint $table) {
            $table->dropIndex('idx_viewings_staff');
            $table->dropIndex('idx_viewings_property');
        });

        Schema::table('client_requests', function (Blueprint $table) {
            $table->dropIndex('idx_client_requests_property');
            $table->dropIndex('idx_client_requests_status');
        });
    }
};
