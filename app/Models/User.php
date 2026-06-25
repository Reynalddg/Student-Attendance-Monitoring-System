<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $primaryKey = 'user_id';
    public $timestamps = false;
    protected $fillable = ['first_name', 'middle_name', 'last_name', 'suffix' ,'phone_number' ,'role', 'email', 'password', 'image', 'date_created', 'date_archived'];

    public function scopeActive($query)
    {
        return $query->whereNull('date_archived');
    }
public function section()
{
    return $this->hasOne(Section::class, 'user_id', 'user_id');
}

    
    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function activeSection()
{
    return $this->hasOne(Section::class, 'user_id', 'user_id')
                ->whereNull('date_archived');
}


}


