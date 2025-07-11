<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AdminLayer extends Model
{
    protected $fillable = ['name', 'type', 'description', 'table_name', 'is_active'];

    public function getFeatureTable()
    {
        return $this->table_name;
    }
}

