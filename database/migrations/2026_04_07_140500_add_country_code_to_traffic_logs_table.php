<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('traffic_logs', function (Blueprint $table) {
            if (!Schema::hasColumn('traffic_logs', 'country_code')) {
                $table->string('country_code', 8)->nullable()->after('country');
            }
        });
    }

    public function down(): void
    {
        Schema::table('traffic_logs', function (Blueprint $table) {
            if (Schema::hasColumn('traffic_logs', 'country_code')) {
                $table->dropColumn('country_code');
            }
        });
    }
};
