<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('blocked_ips', function (Blueprint $table) {
            $table->string('scope_type')->default('global')->after('ip'); // global|domain
            $table->unsignedBigInteger('scope_value')->nullable()->after('scope_type'); // domain_id nếu scope=domain
            $table->timestamp('expires_at')->nullable()->after('reason');
            $table->string('source')->default('manual')->after('expires_at'); // manual|auto
            $table->index(['ip', 'scope_type', 'scope_value'], 'blocked_ips_scope_idx');
            $table->index(['expires_at'], 'blocked_ips_expires_idx');
        });
    }

    public function down(): void
    {
        Schema::table('blocked_ips', function (Blueprint $table) {
            $table->dropIndex('blocked_ips_scope_idx');
            $table->dropIndex('blocked_ips_expires_idx');
            $table->dropColumn(['scope_type', 'scope_value', 'expires_at', 'source']);
        });
    }
};
