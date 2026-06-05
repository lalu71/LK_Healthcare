<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PrescriptionResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'appointment_id' => $this->appointment_id,
            'prescription_code' => $this->prescription_code,
            'diagnosis' => $this->diagnosis,
            'advice' => $this->advice,
            'status' => $this->status,
            'payment_status' => $this->payment_status ?? 'pending',
            'created_at' => $this->created_at->toIso8601String(),
            'doctor' => new DoctorResource($this->whenLoaded('doctor')),
            'patient' => new PatientResource($this->whenLoaded('patient')),
            'items' => $this->whenLoaded('items', fn () => $this->items->map(fn ($i) => [
                'id' => $i->id,
                'medicine_name' => $i->medicine_name,
                'dosage' => $i->dosage,
                'frequency' => $i->frequency,
                'duration' => $i->duration,
                'instructions' => $i->instructions,
            ])),
        ];
    }
}
