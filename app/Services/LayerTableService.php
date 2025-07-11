<?php

namespace App\Services;

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;

class LayerTableService
{
    // App\Services\LayerTableService.php

public function createLayerTable(string $tableName, string $geometryType = 'polygon'): bool
{
    if (!in_array($geometryType, ['polygon', 'line', 'point'])) {
        throw new \InvalidArgumentException("Invalid geometry type: $geometryType");
    }

    if (!Schema::hasTable($tableName)) {
        Schema::create($tableName, function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('code')->nullable();
            $table->string('status')->default('active');
            $table->geometry('geometry');
            $table->json('properties')->nullable(); // ✅ Add this
            $table->timestamps();
            $table->softDeletes();
            $table->spatialIndex('geometry');
        });

       
        return true;
    }

    return false;
}

}
