<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
   public function up(): void
{
    Schema::table('domains', function (Blueprint $table) {
        // Thêm cột is_active, kiểu boolean (true/false), mặc định là true (ON)
        $table->boolean('is_active')->default(true);
    });
}
};
