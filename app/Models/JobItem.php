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

    public function scopeGetQueryWithRelations(Builder $query, $q)
    {
        return $query->with([
                'tags',
                'company',
                'company.location',
                'company.tags',
                'company.user'
            ])
            ->where('title', 'like', '%'.$q.'%')
            ->orWhere('description', 'like', '%'.$q.'%')
            ->orderBy('id', 'desc')->paginate(10);
    }

    public function scopeSearchJobs(Builder $query, $request)
    {

        if ($request->filled('q')) {
            $query->where(function ($q) use ($request) {
                $q->where('title', 'like', '%'.$request->q.'%')
                    ->orWhere('description', 'like', '%'.$request->q.'%');
            });
        }

        $jobs = $query->with('company')->paginate($request->get('per_page', 5));

        return $jobs;
    }
}
