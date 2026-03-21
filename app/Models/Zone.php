<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Zone extends Model
{
    protected $fillable = ['name'];

    public function regions()
    {
        return $this->hasMany(Region::class);
    }

    public function users()
    {
        return $this->hasMany(User::class);
    }
}
