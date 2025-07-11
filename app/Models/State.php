<?php

// app/Models/State.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class State extends Model
{
    protected $fillable = ['name', 'country_id', 'status', 'boundary'];

    public function country()
    {
        return $this->belongsTo(Country::class);
    }

    // public function districts()
    // {
    //     return $this->hasMany(District::class);
    // }
}
