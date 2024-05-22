<?php

namespace App\Models;

use App\Traits\Taggable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    use HasFactory, Taggable;

    protected $fillable = [
        'title',
        'description',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function jobItem()
    {
        return $this->hasMany(JobItem::class);
    }

    public function scopeOrderByIdDesc(Builder $query)
    {
        return $query->orderBy('id', 'desc')->paginate(10);
    }
}
