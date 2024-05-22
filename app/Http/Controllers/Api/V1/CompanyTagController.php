<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\CompanyResource;
use App\Models\Tag;
use Illuminate\Http\Request;

class CompanyTagController extends Controller
{
    public function index($tagId)
    {
        $tag = Tag::findOrFail($tagId);
        return CompanyResource::collection($tag->company);
    }
}
