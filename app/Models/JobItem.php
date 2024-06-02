<?php

namespace App\Models;

use App\Traits\Taggable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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


    // public function scopeSearch(Builder $query, $search)
    // {
    //     return $query->where('title', 'like', '%' . $search . '%')
    //         ->orWhere('description', 'like', '%' . $search . '%');
    // }

    public function scopeOrderByIdDesc(Builder $query)
    {
        return $query->orderBy('id', 'desc')->paginate(10);
    }

    protected static function boot()
    {
        parent::boot();

        static::deleting(function ($jobItem) {
            $jobItem->tags()->detach();
        });
    }
}
