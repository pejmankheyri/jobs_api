<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Company\StoreImageRequest;
use App\Models\Company;
use App\Models\Image;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;

class CompanyImagesController extends Controller
{
    public function store(StoreImageRequest $request, $id)
    {
        if ($request->hasFile('images')) {

            $company = Company::find($id);

            // Gate::authorize('update', $company);

            $validated = $request->validated();
            $images = $validated['images'];

            foreach ($images as $key => $image) {
                $imageName = time().'_'.$key.'.'.$image->extension();

                $image->storeAs('images/company/'.$id, $imageName, 'public');

                $company->images()->save(
                    Image::create([
                        'path' => 'images/company/'.$id.'/'.$imageName,
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

        // Gate::authorize('deleteImage', $company);

        $image->delete();
        Storage::disk('public')->delete($image->path);

        return response()->json(['message' => 'Image deleted']);
    }
}
