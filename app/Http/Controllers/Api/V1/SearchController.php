<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use App\Http\Resources\JobItemResource;
use App\Http\Resources\LocationResource;
use App\Models\JobItem;
use App\Models\Location;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    public function jobs(Request $request)
    {
        $jobs = JobItem::searchJobs($request);

        return JobItemResource::collection($jobs);
    }

    public function locations(Request $request)
    {

        $locations = Location::searchLocations($request);

        return LocationResource::collection($locations);
    }
}
