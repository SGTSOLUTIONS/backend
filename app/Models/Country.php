<?php

// app/Models/Country.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Country extends Model
{
    protected $fillable = ['name', 'code', 'status', 'boundary'];

    public function states()
    {
        return $this->hasMany(State::class);
    }


}
