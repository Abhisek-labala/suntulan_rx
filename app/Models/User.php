<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'username',
        'password',
        'plain_password',
        'role',
        'employee_id',
        'prefix',
        'designation_id',
        'hq_id',
        'region_id',
        'zone_id',
        'reporting_to_id',
    ];

    public function reportingTo()
    {
        return $this->belongsTo(User::class, 'reporting_to_id');
    }

    public function subordinates()
    {
        return $this->hasMany(User::class, 'reporting_to_id');
    }

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'password' => 'hashed',
        ];
    }

    public function designation()
    {
        return $this->belongsTo(Designation::class);
    }

    public function hq()
    {
        return $this->belongsTo(Hq::class);
    }

    public function region()
    {
        return $this->belongsTo(Region::class);
    }

    public function zone()
    {
        return $this->belongsTo(Zone::class);
    }
}
