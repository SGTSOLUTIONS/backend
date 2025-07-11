<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Workspace extends Model
{
    protected $fillable = ['user_id', 'name', 'description'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function layers()
    {
        return $this->hasMany(WorkspaceLayer::class);
    }
}

