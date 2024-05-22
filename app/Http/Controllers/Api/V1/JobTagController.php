<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\JobItemResource;
use App\Models\Tag;
use Illuminate\Http\Request;

class JobTagController extends Controller
{
    public function index($tagId)
    {
        $tag = Tag::findOrFail($tagId);
        return JobItemResource::collection($tag->jobItem);
    }
}
