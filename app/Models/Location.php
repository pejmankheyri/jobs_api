<?php

namespace App\Models;

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
}
