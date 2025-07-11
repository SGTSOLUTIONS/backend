<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserLayer extends Model
{
    protected $fillable = [
        'user_id', 'name', 'type', 'original_filename', 'file_format', 'is_public', 'table_name'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getFeatureTable()
    {
        return $this->table_name;
    }
}
