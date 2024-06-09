<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\JobItem\ApplyRequest;
use App\Http\Requests\JobItem\StoreRequest;
use App\Http\Requests\JobItem\UpdateRequest;
use App\Http\Resources\JobApplicantsResource;
use App\Http\Resources\JobItemResource;
use App\Models\JobItem;
use App\Models\Tag;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Gate;

class JobItemController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if ($request->q) {
            $jobs = JobItem::getQueryWithRelations($request->q);
        } else {
            $jobs = Cache::remember('jobs', 60, function () use ($request) {
                return JobItem::getQueryWithRelations($request->q);
            });
        }

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
        $jobItem = Cache::remember("job-{$id}", 60, function () use ($id) {
            return JobItem::with(['tags', 'company'])->findOrFail($id);
        });

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
                'id' => $jobItem->id,
            ]),
        ]);
    }

    public function apply(ApplyRequest $request, $jobId)
    {
        // Authenticate the user
        $user = Auth::user();
        // Find the job
        $job = JobItem::findOrFail($jobId);

        Gate::authorize('apply', $job);

        $validated = $request->validated();

        // Check if the user has already applied
        if ($job->users()->where('user_id', $user->id)->exists()) {
            return response()->json(['message' => __('message.already_applied')], 400);
        }

        // Attach the user to the job
        $job->users()->attach($user->id);
        // add message to the pivot table
        $job->users()->updateExistingPivot($user->id, ['message' => $validated['message']]);

        return response()->json(['message' => __('message.job_applied')], 200);
    }

    public function applicants($jobId)
    {
        $job = JobItem::findOrFail($jobId);
        Gate::authorize('viewApplicants', $job);

        $applicants = $job->users()->get();

        return JobApplicantsResource::collection($applicants);
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
