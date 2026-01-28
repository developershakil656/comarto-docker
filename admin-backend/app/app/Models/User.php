<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;
use Illuminate\Support\Facades\Cache;


class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'profile',
        'number',
        'email',
        'status',
        'provider',
        'provider_id',
        'password',
        'location_id',
        'address',
        'post_code',
        'last_seen'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password'
    ];

    public function isOnline(): bool
    {
        if (!$this->last_seen) {
            return false; // user never updated last_seen
        }

        // Ensure last_seen is always a Carbon instance
        $lastSeen = $this->last_seen instanceof \Carbon\Carbon
            ? $this->last_seen
            : \Carbon\Carbon::parse($this->last_seen);

        return $lastSeen->gt(now()->subMinutes(5)); // online if seen within last 5 minutes
    }

    public function business()
    {
        return $this->hasOne(Business::class, 'user_id', 'id');
    }


    public function location()
    {
        return $this->belongsTo(Location::class, 'location_id', 'id');
    }


    public function account_verification()
    {
        return $this->hasOne(AccountVerification::class, 'user_id', 'id');
    }
}
