<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\Image;
use Illuminate\Http\Request;

class CompanyImagesController extends Controller
{
    public function store(Request $request, $id)
    {
        if ($request->hasFile('images')) {
            $request->validate([
                'images' => 'required',
                'images.*' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048'
            ]);
            $images = $request->file('images');

            $company = Company::find($id);

            foreach ($images as $key=>$image ) {
                $imageName = time() . '_' . $key . '.' . $image->extension();
                $image->move(public_path('images/companies/'. $id), $imageName);

                $company->images()->save(
                    Image::create([
                        'path' => $imageName,
                    ])
                );
            }
            $responseMessage = 'upload success';
        } else {
            $responseMessage = 'No file for upload';
        }
        return response()->json(['message' => $responseMessage]);
    }

    public function destroy($id)
    {
        $image = Image::find($id);
        $image->delete();
        unlink(public_path('images/companies/'. $image->company_id . '/' . $image->path));
        return response()->json(['message' => 'Image deleted']);
    }
}
