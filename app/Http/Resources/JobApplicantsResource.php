<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class JobApplicantsResource extends JsonResource
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
            'message' => $this->pivot->message,
            'create_dates' => [
                'created_at_human' => $this->pivot->created_at->diffForHumans(),
                'created_at' => $this->pivot->created_at,
            ],
            'update_dates' => [
                'updated_at_human' => $this->pivot->updated_at->diffForHumans(),
                'updated_at' => $this->pivot->updated_at,
            ],
            'user' => new UserResource($this),
        ];
    }
}
