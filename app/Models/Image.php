<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Image extends Model
{
    use HasFactory;

    protected $fillable = ['path', 'company_id'];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }
}
