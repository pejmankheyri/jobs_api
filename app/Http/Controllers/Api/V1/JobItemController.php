<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\JobItem\StoreRequest;
use App\Http\Requests\JobItem\UpdateRequest;
use App\Http\Resources\JobItemResource;
use App\Models\JobItem;
use App\Models\Tag;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

class JobItemController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $jobs = JobItem::with(['tags', 'company','company.location'])->orderByIdDesc();
        return JobItemResource::collection($jobs);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreRequest $request)
    {
        $validated = $request->validated();

        $jobItem = new JobItem();
        $jobItem->title = $validated['title'];
        $jobItem->description = $validated['description'];
        $jobItem->company_id = $validated['company_id'];
        $jobItem->save();

        $tagNames = $validated['tags'];
        $tags = $this->getTags($tagNames);
        $jobItem->tags()->sync($tags);

        return new JobItemResource($jobItem->load(['tags', 'company']));
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $jobItem = JobItem::with(['tags', 'company'])->findOrFail($id);
        return new JobItemResource($jobItem);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateRequest $request, $id)
    {
        $jobItem = JobItem::findOrFail($id);
        Gate::authorize('update', $jobItem);

        $validated = $request->validated();

        $jobItem->title = $validated['title'];
        $jobItem->description = $validated['description'];
        $jobItem->company_id = $jobItem->company_id;

        $jobItem->save();

        $tagNames = $validated['tags'];
        $jobItem->tags()->detach();
        $tags = $this->getTags($tagNames);
        $jobItem->tags()->sync($tags);

        return new JobItemResource($jobItem->load(['tags', 'company']));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $jobItem = JobItem::findOrFail($id);
        Gate::authorize('delete', $jobItem);
        $jobItem->delete();
        return response()->json([
            'message' => __('message.job_removed', [
                'title' => $jobItem->title,
                'id' => $jobItem->id
            ])
        ]);
    }

    /**
     * Get tags from tag names.
     */
    private function getTags($tagNames)
    {
        $tags = [];
        foreach ($tagNames as $tagName) {
            $tag = Tag::firstOrCreate(['name' => $tagName]);
            $tags[] = $tag->id;
        }
        return $tags;
    }
}
