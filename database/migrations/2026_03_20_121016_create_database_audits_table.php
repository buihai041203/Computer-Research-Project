<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
public function up(): void
{
Schema::create('database_audits', function (Blueprint $table) {
$table->id();
$table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
$table->string('site_name');
$table->string('action'); // query, update, delete...
$table->longText('query_text')->nullable();
$table->string('ip')->nullable();
$table->timestamps();
});
}

public function down(): void
{
Schema::dropIfExists('database_audits');
}
};
