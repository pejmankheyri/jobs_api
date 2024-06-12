<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Location extends Model
{
    use HasFactory;

    protected $fillable = [
        'address',
        'city',
        'state',
        'country',
        'zip_code',
        'company_id',
    ];

    /**
     * Get the company that owns the location.
     */
    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function scopeSearchLocations(Builder $query, $request)
    {
        if ($request->filled('q')) {
            $query->where(function ($q) use ($request) {
                $q->where('state', 'like', '%'.$request->q.'%')
                    ->orWhere('city', 'like', '%'.$request->q.'%')
                    ->orWhere('country', 'like', '%'.$request->q.'%');
            });
        }

        $locations = $query->paginate($request->get('per_page', 5));

        return $locations;
    }
}
