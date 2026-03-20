<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('domains', 'user_id')) {
            Schema::table('domains', function (Blueprint $table) {
                $table->foreignId('user_id')
                      ->nullable()
                      ->after('agent_key')
                      ->constrained()
                      ->cascadeOnDelete();
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('domains', 'user_id')) {
            Schema::table('domains', function (Blueprint $table) {
                $table->dropForeign(['user_id']);
                $table->dropColumn('user_id');
            });
        }
    }
};