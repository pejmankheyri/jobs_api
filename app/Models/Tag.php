<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tag extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
    ];

    public function jobItem()
    {
        return $this->morphedByMany(JobItem::class, 'taggable')->withTimestamps();
    }

    public function company()
    {
        return $this->morphedByMany(Company::class, 'taggable')->withTimestamps();
    }
}
