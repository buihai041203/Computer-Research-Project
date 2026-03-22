<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('site_databases', function (Blueprint $table) {
            if (!Schema::hasColumn('site_databases', 'domain_id')) {
                $table->foreignId('domain_id')->nullable()->after('id')->constrained('domains')->nullOnDelete();
            }
        });

        // map domain_id theo site_name = domain (nếu trước đây bạn lưu kiểu đó)
        DB::statement("
            UPDATE site_databases sd
            JOIN domains d ON d.domain = sd.site_name
            SET sd.domain_id = d.id
            WHERE sd.domain_id IS NULL
        ");
    }

    public function down(): void
    {
        Schema::table('site_databases', function (Blueprint $table) {
            if (Schema::hasColumn('site_databases', 'domain_id')) {
                $table->dropConstrainedForeignId('domain_id');
            }
        });
    }
};
