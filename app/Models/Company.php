<?php

namespace App\Models;

use App\Traits\Taggable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\File;

class Company extends Model
{
    use HasFactory, Taggable;

    protected $fillable = [
        'title',
        'description',
        'logo',
    ];

    /**
     * Get the user that owns the company.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the job items for the company.
     */
    public function jobItem()
    {
        return $this->hasMany(JobItem::class);
    }

    /**
     * Get the locations for the company.
     */
    public function location()
    {
        return $this->hasMany(Location::class);
    }

    /**
     * Get the images for the company.
     */
    public function images()
    {
        return $this->hasMany(Image::class);
    }

    /**
     * Scope a query to order by id in descending order.
     */
    public function scopeOrderByIdDesc(Builder $query)
    {
        return $query->orderBy('id', 'desc')->paginate(10);
    }

    protected static function boot()
    {
        parent::boot();

        static::deleting(function ($company) {
            $company->images->each->delete();
            $company->location->each->delete();
            $company->jobItem->each->delete();

            $company->tags()->detach();


            $directory = public_path('images/companies/' . $company->id);
            if (File::exists($directory)) {
                File::deleteDirectory($directory);
            }
        });
    }
}
