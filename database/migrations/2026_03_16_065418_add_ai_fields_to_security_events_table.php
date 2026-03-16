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
        Schema::table('security_events', function (Blueprint $table) {
            $table->string('attack_type')->nullable()->after('type');
            $table->string('threat_level')->nullable()->after('attack_type');
            $table->text('ai_analysis')->nullable()->after('description');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('security_events', function (Blueprint $table) {
            $table->dropColumn(['attack_type','threat_level','ai_analysis']);
        });
    }
};
