<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Hq extends Model
{
    protected $fillable = ['name', 'region_id'];

    public function region()
    {
        return $this->belongsTo(Region::class);
    }

    public function users()
    {
        return $this->hasMany(User::class);
    }
}
