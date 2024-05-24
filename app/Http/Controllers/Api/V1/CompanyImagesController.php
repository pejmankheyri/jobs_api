<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Company\StoreImageRequest;
use App\Models\Company;
use App\Models\Image;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class CompanyImagesController extends Controller
{
    public function store(StoreImageRequest $request, $id)
    {
        if ($request->hasFile('images')) {

            $company = Company::find($id);

            Gate::authorize('update', $company);

            $validated = $request->validated();
            $images = $validated['images'];


            foreach ($images as $key=>$image ) {
                $imageName = time() . '_' . $key . '.' . $image->extension();
                $image->move(public_path('images/companies/'. $id), $imageName);

                $company->images()->save(
                    Image::create([
                        'path' => $imageName,
                    ])
                );
            }
            $responseMessage = __('message.company_images_uploaded_successfully');
        } else {
            $responseMessage = __('message.no_file_for_upload');
        }
        return response()->json(['message' => $responseMessage]);
    }

    public function destroy($id)
    {
        $image = Image::find($id);
        $company = Company::find($image->company_id);

        Gate::authorize('delete', $company);

        $image->delete();
        unlink(public_path('images/companies/'. $image->company_id . '/' . $image->path));
        return response()->json(['message' => 'Image deleted']);
    }
}
