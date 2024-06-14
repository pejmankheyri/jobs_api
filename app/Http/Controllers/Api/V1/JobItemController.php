<?php

namespace App\Http\Controllers\Api\V1;

use App\Events\JobApplied;
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
            $jobs = JobItem::getQueryWithRelations($request);
        } else {
            $jobs = Cache::tags(['jobs'])->remember('jobsList', 60, function () use ($request) {
                return JobItem::getQueryWithRelations($request);
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
            return JobItem::with(['tags', 'company', 'company.location', 'company.images'])->findOrFail($id);
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
        $user = Auth::user();

        $job = JobItem::findOrFail($jobId);

        Gate::authorize('apply', $job);

        $validated = $request->validated();
        $message = $validated['message'];

        event(new JobApplied($job, $user, $message));

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
