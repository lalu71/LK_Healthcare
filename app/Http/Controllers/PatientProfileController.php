<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Models\Patient;

class PatientProfileController extends Controller
{
    public function edit(Request $request)
    {
        $patient = $request->user()->patient ?? new Patient();
        return view('patient.profile', compact('patient'));
    }

    public function update(Request $request)
    {
        $user = $request->user();

        $validated = $request->validate([
            'dob' => 'nullable|date',
            'gender' => 'nullable|in:male,female,other',
            'blood_group' => 'nullable|string|max:5',
            'allergies' => 'nullable|string',
            'medical_history' => 'nullable|string',
            'emergency_contact' => 'nullable|string|max:20',
            'aadhaar_number' => [
                'nullable', 'string', 'max:20',
                Rule::unique('patients', 'aadhaar_number')->ignore($user->patient?->id),
            ],
        ], [
            'aadhaar_number.unique' => __('This Aadhaar number is already registered with another patient.'),
        ]);
        
        $data = $validated;
        if (!$user->patient || !$user->patient->patient_id) {
            do {
                $patientId = 'LK-' . random_int(1000000, 9999999);
            } while (
                Patient::where('patient_id', $patientId)->exists()
            );

            $data['patient_id'] = $patientId;
        }

        if ($user->patient) {
            $user->patient->update($data);
        } else {
            $user->patient()->create($data);
        }

        return redirect()->route('dashboard')->with('success', 'Medical profile updated successfully!');
    }
}
