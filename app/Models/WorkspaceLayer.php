<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WorkspaceLayer extends Model
{
    protected $fillable = [
        'workspace_id', 'layer_type', 'layer_id', 'layer_order', 'is_visible', 'style_settings'
    ];

    public function workspace()
    {
        return $this->belongsTo(Workspace::class);
    }

    public function layer()
    {
        return $this->morphTo('layer', 'layer_type', 'layer_id');
    }
}
