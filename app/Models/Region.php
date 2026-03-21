<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Region extends Model
{
    protected $fillable = ['name', 'zone_id'];

    public function zone()
    {
        return $this->belongsTo(Zone::class);
    }

    public function hqs()
    {
        return $this->hasMany(Hq::class);
    }

    public function users()
    {
        return $this->hasMany(User::class);
    }
}
