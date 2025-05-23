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

        /**
     * Get jobs that user has saved/bookmarked
     */
    public function savedJobs()
    {
        return $this->belongsToMany(JobItem::class, 'job_saved')
                    ->withTimestamps();
    }

    /**
     * Save a job for later viewing
     */
    public function saveJob(JobItem $job): void
    {
        $this->savedJobs()->syncWithoutDetaching([$job->id]);
    }

    /**
     * Remove a job from saved jobs
     */
    public function unsaveJob(JobItem $job): void
    {
        $this->savedJobs()->detach($job->id);
    }

    /**
     * Check if a job is saved by user
     */
    public function hasSavedJob(JobItem $job): bool
    {
        return $this->savedJobs()->where('job_item_id', $job->id)->exists();
    }

    /**
     * Get saved jobs with filtering and pagination
     */
    public function scopeSavedJobs(Builder $query, $request)
    {
        $user = Auth::user();

        $perPage = $request->query('per_page', 10);
        $sort = $request->query('sort', 'created_at');
        $order = $request->query('order', 'desc');
        $search = $request->query('q', '');

        if (!in_array($order, ['asc', 'desc'])) {
            return response()->json(['message' => 'Invalid sort parameter'], 400);
        }

        $output = $user->savedJobs()
            ->when($search, function ($query, $search) {
                return $query->where(function ($q) use ($search) {
                    $q->where('title', 'like', '%'.$search.'%')
                        ->orWhere('description', 'like', '%'.$search.'%');
                });
            })
            ->with(['tags', 'company', 'company.location'])
            ->orderBy('job_saved.'.$sort, $order)
            ->paginate($perPage);

        return $output;
    }

    public function scopeOrderByIdDesc(Builder $query)
    {
        return $query->orderBy('id', 'desc')->paginate(10);
    }

    public function scopeAppliedJobs(Builder $query, $request)
    {
        $user = Auth::user();

        $perPage = $request->query('per_page', 10);

        $sort = $request->query('sort', 'id');
        $order = $request->query('order', 'desc');
        $search = $request->query('q', '');

        if (! in_array($order, ['asc', 'desc'])) {
            return response()->json(['message' => 'Invalid sort parameter'], 400);
        }

        $output = $user->jobs()
            ->when($search, function ($query, $search) {
                return $query->where(function ($q) use ($search) {
                    $q->where('title', 'like', '%'.$search.'%')
                        ->orWhere('description', 'like', '%'.$search.'%');
                });
            })
            ->orderBy($sort, $order)
            ->paginate($perPage);

        return $output;
    }

    public function scopeCompaniesList(Builder $query, $request)
    {
        $user = Auth::user();

        $perPage = $request->query('per_page', 10);

        $sort = $request->query('sort', 'id');
        $order = $request->query('order', 'desc');
        $search = $request->query('q', '');

        if (! in_array($order, ['asc', 'desc'])) {
            return response()->json(['message' => 'Invalid sort parameter'], 400);
        }

        $output = $user->companies()
            ->when($search, function ($query, $search) {
                return $query->where(function ($q) use ($search) {
                    $q->where('title', 'like', '%'.$search.'%')
                        ->orWhere('description', 'like', '%'.$search.'%');
                });
            })
            ->with(['location', 'tags', 'jobItem'])
            ->orderBy($sort, $order)
            ->paginate($perPage);

        return $output;
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

            if ($user->company && $user->company->count() > 0) {
                $user->company->each->delete();
            }
        });
    }
}
