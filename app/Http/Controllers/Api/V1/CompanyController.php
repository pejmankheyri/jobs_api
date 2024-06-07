<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Company\StoreLogoRequest;
use App\Http\Requests\Company\StoreRequest;
use App\Http\Requests\Company\UpdateRequest;
use App\Http\Resources\CompanyResource;
use App\Models\Company;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;

class CompanyController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $comapanies = Company::with(['location','tags','user','jobItem'])->orderByIdDesc();
        return CompanyResource::collection($comapanies);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreRequest $request)
    {
        $validated = $request->validated();

        $company = new Company();
        $company->title = $validated['title'];
        $company->description = $validated['description'];
        $company->rating = $validated['rating'];
        $company->website = $validated['website'];
        $company->employes = $validated['employes'];
        $company->user_id = Auth::id();

        $company->save();

        return new CompanyResource($company->load(['location','tags','user','jobItem']));
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $company = Company::with(['location','tags','user','jobItem'])->findOrFail($id);
        return new CompanyResource($company);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateRequest $request, $id)
    {
        $company = Company::findOrFail($id);

        Gate::authorize('update', $company);

        $validated = $request->validated();

        $company->title = $validated['title'];
        $company->description = $validated['description'];
        $company->rating = $validated['rating'];
        $company->website = $validated['website'];
        $company->employes = $validated['employes'];
        $company->user_id = Auth::id();

        $company->save();

        return new CompanyResource($company->load(['location','tags','user','jobItem']));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $company = Company::findOrFail($id);
        Gate::authorize('delete', $company);
        $company->delete();
        return response()->json([
            'message' => __('message.company_removed', [
                'title' => $company->title,
                'id' => $company->id
            ])
        ]);
    }

    /**
     * Upload company logo.
     */
    public function uploadLogo(StoreLogoRequest $request, $id)
    {
        $company = Company::findOrFail($id);
        Gate::authorize('storeLogo', $company);

        $validated = $request->validated();

        if ($request->hasFile('logo')) {
            $logo = $request->file('logo');
            $filename = time() . '.' . $logo->getClientOriginalExtension();
            $path = $logo->storeAs('logos', $filename, 'public');

            if ($company->logo) {
                Storage::disk('public')->delete($company->logo);
            }

            $company->logo = $path;
            $company->save();
        }

        return new CompanyResource($company->load(['location','tags','user','jobItem']));
    }
}
