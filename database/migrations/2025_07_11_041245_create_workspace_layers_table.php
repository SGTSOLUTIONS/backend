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
        Schema::create('workspace_layers', function (Blueprint $table) {
    $table->id();
    $table->foreignId('workspace_id')->constrained();
    $table->string('layer_type'); // 'admin' or 'user'
    $table->unsignedBigInteger('layer_id'); // FK to admin_layers/user_layers
    $table->integer('layer_order')->default(0);
    $table->boolean('is_visible')->default(true);
    $table->json('style_settings')->nullable();
    $table->timestamps();
});

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('workspace_layers');
    }
};
