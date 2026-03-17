<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('traffic_logs', function (Blueprint $table) {
            if (!Schema::hasColumn('traffic_logs', 'is_bot')) {
                $table->boolean('is_bot')->default(false)->after('ip');
            }
            if (!Schema::hasColumn('traffic_logs', 'country')) {
                $table->string('country')->default('Unknown')->after('ip');
            }
            if (!Schema::hasColumn('traffic_logs', 'threat')) {
                $table->string('threat')->default('LOW')->after('is_bot');
            }
        });
    }

    public function down(): void
    {
        Schema::table('traffic_logs', function (Blueprint $table) {
            $table->dropColumn(['is_bot', 'country', 'threat']);
        });
    }
};