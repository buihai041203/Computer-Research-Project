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
        Schema::table('traffic_logs', function (Blueprint $table) {
            if (!Schema::hasColumn('traffic_logs', 'country')) {
                $table->string('country')->nullable()->after('ip');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('traffic_logs', function (Blueprint $table) {
            if (Schema::hasColumn('traffic_logs', 'country')) {
                $table->dropColumn('country');
            }
        });
    }
};
