<?php

namespace App\Http\Controllers;

use App\Models\Doctor;
use App\Models\Specialization;
use Illuminate\Http\Request;

class DoctorProfileController extends Controller
{
    public function edit(Request $request)
    {
        $doctor = $request->user()->doctor ?? new Doctor();
        $specializations = Specialization::all();
        return view('doctor.profile', compact('doctor', 'specializations'));
    }

    public function update(Request $request)
    {
        $user = $request->user();
        if (!$user->hasRole('doctor')) {
            return redirect()->route('dashboard')->with('error', 'Unauthorized.');
        }

        $validated = $request->validate([
            'specialization_id' => 'required|exists:specializations,id',
            'experience_years' => 'required|integer|min:0|max:60',
            'consultation_fee' => 'required|numeric|min:0',
            'qualification' => 'nullable|string|max:150',
            'clinic_address' => 'nullable|string|max:255',
            'bio' => 'nullable|string|max:2000',
        ]);

        if ($user->doctor) {
            $user->doctor->update($validated);
        } else {
            $user->doctor()->create(array_merge($validated, ['is_active' => true]));
        }

        return redirect()->route('dashboard')->with('success', 'Professional profile updated.');
    }
}
