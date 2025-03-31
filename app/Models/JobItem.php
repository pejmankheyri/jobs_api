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

    public function scopeGetQueryWithRelations(Builder $query, $request)
    {
        $query->with([
            'tags',
            'company',
            'company.location',
            'company.tags',
            'company.user',
            'company.images',
        ]);

        // Add job title and description search conditions
        $query->where(function ($query) use ($request) {
            $query->where('title', 'like', '%'.$request->q.'%')
                ->orWhere('description', 'like', '%'.$request->q.'%');
        });

        // Add location search conditions if 'location' parameter is provided
        if (! empty($request->location)) {
            $locations = explode(',', $request->location);
            $country = $locations[0] ?? null;
            $state = $locations[1] ?? null;
            $city = $locations[2] ?? null;

            $query->orWhereHas('company.location', function ($query) use ($country, $state, $city) {
                if ($country) {
                    $query->where('country', 'like', '%'.$country.'%');
                }
                if ($state) {
                    $query->where('state', 'like', '%'.$state.'%');
                }
                if ($city) {
                    $query->where('city', 'like', '%'.$city.'%');
                }
            });
        }

        // Order by ID in descending order and paginate
        return $query->orderBy('id', 'desc')->paginate($request->per_page ?? 10);
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
