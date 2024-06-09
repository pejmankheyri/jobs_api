<?php

namespace App\Models;

use App\Traits\Taggable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class JobItem extends Model
{
    use HasFactory, Taggable;

    protected $fillable = [
        'title',
        'description',
    ];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function tags()
    {
        return $this->morphToMany(Tag::class, 'taggable')->withTimestamps();
    }

    public function users()
    {
        return $this->belongsToMany(User::class, 'job_user')->withPivot('message')->withTimestamps();
    }

    public function scopeGetQueryWithRelations(Builder $query, $q)
    {
        return $query->with(['tags', 'company', 'company.location'])
            ->where('title', 'like', '%'.$q.'%')
            ->orWhere('description', 'like', '%'.$q.'%')
            ->orderBy('id', 'desc')->paginate(10);
    }

    protected static function boot()
    {
        parent::boot();

        static::deleting(function ($jobItem) {
            $jobItem->tags()->detach();

            Cache::forget('jobs');
        });

        static::updating(function ($jobItem) {
            Cache::forget("jobs-{$jobItem->id}");
        });
    }
}
