<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DoctorResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'user' => new UserResource($this->whenLoaded('user')),
            'specialization' => $this->whenLoaded('specialization', fn () => [
                'id' => $this->specialization->id,
                'name' => $this->specialization->name,
            ]),
            'qualification' => $this->qualification,
            'experience_years' => (int) $this->experience_years,
            'consultation_fee' => (float) $this->consultation_fee,
            'rating' => 4.8, // placeholder until reviews module exists
            'reviews_count' => 0,
        ];
    }
}
