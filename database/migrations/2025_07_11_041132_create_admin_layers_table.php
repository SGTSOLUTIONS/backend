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
        Schema::create('admin_layers', function (Blueprint $table) {
    $table->id();
    $table->string('name')->unique(); // "India Wards"

    $table->text('description')->nullable();
    $table->string('table_name')->unique(); // dynamic table to hold geometry
    $table->boolean('is_active')->default(true);
    $table->timestamps();
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('admin_layers');
    }
};
