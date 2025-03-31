<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\JobItemResource;
use App\Models\Tag;
use Illuminate\Http\Request;

class JobTagController extends Controller
{
    public function index(Request $request, $tagId)
    {
        $tag = Tag::findOrFail($tagId);

        $perPage = $request->per_page ?? 15;
        $jobs = $tag->jobItem()->with(['tags', 'company'])
            ->orderBy('id', 'desc')
            ->paginate($perPage);

        return JobItemResource::collection($jobs);
    }
}
