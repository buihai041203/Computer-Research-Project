<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
public function up(): void
{
Schema::create('site_databases', function (Blueprint $table) {
$table->id();
$table->string('site_name')->unique(); // ví dụ: lms
$table->string('db_connection')->default('mysql');
$table->string('db_host')->default('127.0.0.1');
$table->unsignedInteger('db_port')->default(3306);
$table->string('db_name')->nullable();
$table->string('db_user')->nullable();
$table->text('db_password')->nullable(); // sẽ mã hóa
$table->boolean('is_active')->default(true);
$table->timestamps();
});
}

public function down(): void
{
Schema::dropIfExists('site_databases');
}
};
