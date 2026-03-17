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
        Schema::create('security_events', function (Blueprint $table) {

            $table->id();

            $table->ipAddress('ip');

            $table->string('domain')->nullable();

            $table->string('type');

            $table->string('attack_type')->nullable();

            $table->string('threat_level')->nullable();

            $table->text('description');

            $table->json('ai_analysis')->nullable();

            $table->timestamps();

            $table->index(['ip', 'threat_level']);

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('security_events');
    }
};
