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
         Schema::create('countries', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('code', 3)->unique();
            $table->string('status')->default('active');

            // For MySQL:
            $table->geometry('boundary');

            // For PostgreSQL/PostGIS:
            // $table->geography('boundary', 'POLYGON', 4326);

            $table->timestamps();

            // Spatial index
            $table->spatialIndex('boundary');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('countries');
    }
};
