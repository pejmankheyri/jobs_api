<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'phone' => $this->phone,
            'avatar' => Storage::url($this->avatar),
            'cv' => Storage::url($this->cv),
            'role' => $this->roles->first()->name ,
            'companies' => CompanyResource::collection($this->whenLoaded('company')),
        ];
    }
}
