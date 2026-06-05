<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PatientResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'user' => new UserResource($this->whenLoaded('user')),
            'patient_id' => $this->patient_id,
            'dob' => $this->dob?->toDateString(),
            'gender' => $this->gender,
            'blood_group' => $this->blood_group,
            'allergies' => $this->allergies,
            'age' => $this->age,
            'aadhaar' => $this->aadhaar_number,
            'emergency_contact' => $this->emergency_contact,
            'medical_history' => $this->medical_history,
        ];
    }
}
