<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Auth;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'phone',
        'password',
        'avatar',
        'cv',
    ];

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
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function companies()
    {
        return $this->hasMany(Company::class);
    }

    public function roles()
    {
        return $this->belongsToMany(Role::class);
    }

    public function jobs()
    {
        return $this->belongsToMany(JobItem::class, 'job_user')->withPivot('message')->withTimestamps();
    }

    public function scopeOrderByIdDesc(Builder $query)
    {
        return $query->orderBy('id', 'desc')->paginate(10);
    }

    public function hasRole($roleName)
    {
        return $this->roles()->where('name', $roleName)->exists();
    }

    // get user role
    public function getRoleAttribute()
    {
        return $this->roles->first()->name;
    }

    // is admin
    public function isAdmin()
    {
        return Auth::user()->roles->first()->id === 1;
    }

    protected static function booted(): void
    {
        parent::boot();

        static::deleting(function (User $user) {
            $user->company->each->delete();
        });
    }
}
