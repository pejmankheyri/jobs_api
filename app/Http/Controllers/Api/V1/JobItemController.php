<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\JobItem\StoreRequest;
use App\Http\Requests\JobItem\UpdateRequest;
use App\Http\Resources\JobItemResource;
use App\Models\JobItem;
use Illuminate\Http\Request;

class JobItemController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $jobs = JobItem::with('tags')->with('user')->orderByIdDesc();
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
        $jobItem->user_id = 1;

        $jobItem->save();

        return new JobItemResource($jobItem);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $jobItem = JobItem::findOrFail($id);
        return new JobItemResource($jobItem);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateRequest $request, $id)
    {
        $jobItem = JobItem::findOrFail($id);

        $validated = $request->validated();

        $jobItem->title = $validated['title'];
        $jobItem->description = $validated['description'];
        $jobItem->user_id = 1;

        $jobItem->save();

        return new JobItemResource($jobItem);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $jobItem = JobItem::findOrFail($id);
        $jobItem->delete();
        return response()->json(['message' => 'Job ' . $jobItem->title . ' with id ' . $id . ' removed successfully!'], 200);
    }
}
