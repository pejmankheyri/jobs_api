<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\CompanyResource;
use App\Models\Tag;

class CompanyTagController extends Controller
{
    public function index($tagId)
    {
        $tag = Tag::findOrFail($tagId);

        return CompanyResource::collection($tag->company->load(['location', 'tags', 'user', 'jobItem']));
    }
}
