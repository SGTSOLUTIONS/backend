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
       Schema::create('user_layers', function (Blueprint $table) {
    $table->id();
    $table->foreignId('user_id')->constrained();
    $table->string('name');
    $table->enum('type', ['polygon', 'line', 'point']);
    $table->string('original_filename');
    $table->enum('file_format', ['shp', 'geojson', 'kml']);
    $table->boolean('is_public')->default(false);
    $table->string('table_name'); // to store in a dynamic feature table
    $table->timestamps();

    $table->unique(['user_id', 'name']);
});

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_layers');
    }
};
