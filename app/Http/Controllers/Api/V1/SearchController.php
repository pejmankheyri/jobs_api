<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\JobItem;
use App\Models\Location;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    public function jobs(Request $request)
    {
        $query = JobItem::query();

        // Search by job title or description
        if ($request->filled('q')) {
            $query->where(function($q) use ($request) {
                $q->where('title', 'like', '%' . $request->q . '%')
                  ->orWhere('description', 'like', '%' . $request->q . '%');
            });
        }

        // Pagination
        $jobs = $query->with('company')->paginate($request->get('per_page', 5));

        return response()->json($jobs);
    }

    public function locations(Request $request)
    {
        $query = Location::query();

        // Search by location state or city or country
        if ($request->filled('q')) {
            $query->where(function($q) use ($request) {
                $q->where('state', 'like', '%' . $request->q . '%')
                  ->orWhere('city', 'like', '%' . $request->q . '%')
                  ->orWhere('country', 'like', '%' . $request->q . '%');
            });
        }

        // Pagination
        $locations = $query->paginate($request->get('per_page', 10));

        return response()->json($locations);
    }
}
