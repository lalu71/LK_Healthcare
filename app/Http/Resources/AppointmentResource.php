<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AppointmentResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,

            'appointment_date' => $this->appointment_date?->timezone('Asia/Kolkata')->format('Y-m-d H:i:s'),
            'reason' => $this->reason,
            'status' => $this->status,
            'payment_status' => $this->payment_status,
            'created_at' => $this->created_at?->timezone('Asia/Kolkata')->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at?->timezone('Asia/Kolkata')->format('Y-m-d H:i:s'),
            'doctor' => new DoctorResource($this->whenLoaded('doctor')),
            'patient' => new PatientResource($this->whenLoaded('patient')),
        ];
    }
}
