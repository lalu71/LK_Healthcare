<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\PatientResource;
use Illuminate\Http\Request;

class PatientApiController extends Controller
{
    public function me(Request $request)
    {
        $patient = $request->user()->patient;
        abort_unless($patient, 404, 'Patient profile not found');
        $patient->load('user');
        return response()->json([
            'patient' => (new PatientResource($patient))->resolve(),
        ]);
    }

    public function update(Request $request)
    {
        $user    = $request->user();
        $patient = $user->patient;
        abort_unless($patient, 404, 'Patient profile not found');

        $data = $request->validate([
            // Patient (medical) fields
            'dob' => 'nullable|date',
            'gender' => 'nullable|in:male,female,other',
            'blood_group' => 'nullable|string|max:5',
            'allergies' => 'nullable|string|max:500',
            'medical_history' => 'nullable|string|max:2000',
            'emergency_contact' => 'nullable|string|max:50',
            'aadhaar_number' => 'nullable|string|max:20',
            // User-level updatable fields (optional)
            'name' => 'sometimes|string|max:150',
            'phone' => 'sometimes|nullable|string|max:20',
            'avatar' => 'sometimes|nullable|image|mimes:jpeg,jpg,png,webp|max:2048',
        ]);

        // Update patient (medical) fields
        $patient->update(collect($data)->only([
            'dob', 'gender', 'blood_group', 'allergies',
            'medical_history', 'emergency_contact', 'aadhaar_number',
        ])->toArray());

        // Update user-level fields
        $userDirty = false;
        if (array_key_exists('name', $data))  { $user->name  = $data['name'];  $userDirty = true; }
        if (array_key_exists('phone', $data)) { $user->phone = $data['phone']; $userDirty = true; }

        // Avatar: replace old with new (trait handles delete + store)
        if ($request->hasFile('avatar')) {
            $user->replaceAvatar($request->file('avatar'));
            $userDirty = true;
        }

        if ($userDirty) {
            $user->save();
        }

        $patient->load('user');
        return response()->json([
            'patient' => (new PatientResource($patient))->resolve(),
        ]);
    }
}
