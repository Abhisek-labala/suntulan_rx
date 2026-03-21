<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RxDetail extends Model
{
    protected $fillable = [
        'user_id',
        'zone_id',
        'region_id',
        'hq_id',
        'rx_count',
        'date'
    ];
    public function zone()
    {
        return $this->belongsTo(Zone::class);
    }

    public function region()
    {
        return $this->belongsTo(Region::class);
    }

    public function hq()
    {
        return $this->belongsTo(Hq::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
