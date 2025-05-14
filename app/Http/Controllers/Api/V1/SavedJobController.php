<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\JobItemResource;
use App\Models\JobItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SavedJobController extends Controller
{
    /**
     * Get user's saved jobs with pagination
     */
    public function index(Request $request)
    {
        $user = Auth::user();

        $perPage = $request->query('per_page', 10);
        $sort = $request->query('sort', 'created_at');
        $order = $request->query('order', 'desc');
        $search = $request->query('q', '');

        if (!in_array($order, ['asc', 'desc'])) {
            return response()->json(['message' => 'Invalid sort parameter'], 400);
        }

        $jobs = $user->savedJobs()
            ->when($search, function ($query, $search) {
                return $query->where(function ($q) use ($search) {
                    $q->where('title', 'like', '%'.$search.'%')
                        ->orWhere('description', 'like', '%'.$search.'%');
                });
            })
            ->with(['tags', 'company', 'company.location'])
            ->orderBy("job_saved.{$sort}", $order)
            ->paginate($perPage);

        return JobItemResource::collection($jobs);
    }

    /**
     * Save a job
     */
    public function store($jobId)
    {
        $job = JobItem::findOrFail($jobId);
        Auth::user()->saveJob($job);

        return response()->json([
            'message' => 'Job saved successfully',
        ]);
    }

    /**
     * Remove a job from saved list
     */
    public function destroy($jobId)
    {
        $job = JobItem::findOrFail($jobId);
        Auth::user()->unsaveJob($job);

        return response()->json([
            'message' => 'Job removed from saved list',
        ]);
    }
}
