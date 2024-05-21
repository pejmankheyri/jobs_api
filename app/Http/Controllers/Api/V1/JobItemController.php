<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\JobItemResourse;
use App\Models\JobItem;
use Illuminate\Http\Request;

class JobItemController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $jobs = JobItem::orderBy('id', 'desc')->paginate(10);
        return JobItemResourse::collection($jobs);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $job = JobItem::create([
            'title' => $request->input('title'),
            'description' => $request->input('description')
        ]);

        return new JobItemResourse($job);
    }

    /**
     * Display the specified resource.
     */
    public function show(JobItem $jobItem)
    {
        return new JobItemResourse($jobItem);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(JobItem $jobItem)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $jobItem = JobItem::findOrFail($id);

        $jobItem->update([
            'title' => $request->input('title'),
            'description' => $request->input('description')
        ]);

        return new JobItemResourse($jobItem);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $jobItem = JobItem::findOrFail($id);

        $jobItem->delete();

        return response()->json(['message' => 'Job removed successfully!'], 200);
    }
}
