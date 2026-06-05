<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\DoctorResource;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class DoctorProfileApiController extends Controller
{
    /**
     * One-shot profile update for the mobile app: updates user fields
     * (name, email, phone, avatar) AND doctor fields (specialization,
     * qualification, experience, fee, bio, clinic address) in a single
     * POST request (multipart-friendly for the image upload).
     */
    public function updateProfile(Request $request)
    {
        $user   = $request->user();
        $doctor = $user->doctor;
        abort_unless($doctor, 404, 'Doctor profile not found');

        $data = $request->validate([
            // User-level
            'name' => 'sometimes|required|string|max:150',
            'email' => ['sometimes', 'required', 'email', 'max:150', Rule::unique('users', 'email')->ignore($user->id)],
            'phone' => 'sometimes|nullable|string|max:20',
            'avatar' => 'sometimes|nullable|image|mimes:jpeg,jpg,png,webp|max:2048',
            // Doctor-level
            'specialization_id' => 'sometimes|required|exists:specializations,id',
            'qualification' => 'sometimes|nullable|string|max:200',
            'experience_years' => 'sometimes|nullable|integer|min:0|max:80',
            'consultation_fee' => 'sometimes|nullable|numeric|min:0',
            'bio' => 'sometimes|nullable|string|max:2000',
            'clinic_address' => 'sometimes|nullable|string|max:500',
        ], [
            'email.unique' => __('This email is already in use by another account.'),
        ]);

        // ── User row ──
        foreach (['name', 'email', 'phone'] as $field) {
            if (array_key_exists($field, $data)) {
                $user->{$field} = $data[$field];
            }
        }
        if ($request->hasFile('avatar')) {
            $user->replaceAvatar($request->file('avatar'));
        }
        $user->save();

        // ── Doctor row ──
        $doctor->fill(collect($data)->only([
            'specialization_id', 'qualification', 'experience_years',
            'consultation_fee', 'bio', 'clinic_address',
        ])->toArray());
        $doctor->save();

        $doctor->load('user', 'specialization');

        return response()->json([
            'message' => __('Profile updated successfully.'),
            'doctor' => (new DoctorResource($doctor))->resolve(),
            'extra' => [
                'bio' => $doctor->bio,
                'clinic_address' => $doctor->clinic_address,
            ],
        ]);
    }

    public function me(Request $request)
    {
        $doctor = $request->user()->doctor;
        abort_unless($doctor, 404, 'Doctor profile not found');
        $doctor->load('user', 'specialization');
        return response()->json([
            'doctor' => (new DoctorResource($doctor))->resolve(),
            'extra' => [
                'bio' => $doctor->bio,
                'clinic_address' => $doctor->clinic_address,
            ],
        ]);
    }

    public function update(Request $request)
    {
        $user   = $request->user();
        $doctor = $user->doctor;
        abort_unless($doctor, 404, 'Doctor profile not found');

        $data = $request->validate([
            // Doctor fields
            'specialization_id' => 'nullable|exists:specializations,id',
            'qualification' => 'nullable|string|max:200',
            'experience_years' => 'nullable|integer|min:0|max:80',
            'consultation_fee' => 'nullable|numeric|min:0',
            'bio' => 'nullable|string|max:2000',
            'clinic_address' => 'nullable|string|max:500',
            // User-level
            'name' => 'sometimes|string|max:150',
            'phone' => 'sometimes|nullable|string|max:20',
            'avatar' => 'sometimes|nullable|image|mimes:jpeg,jpg,png,webp|max:2048',
        ]);

        // Doctor row
        $doctor->update(collect($data)->only([
            'specialization_id', 'qualification', 'experience_years',
            'consultation_fee', 'bio', 'clinic_address',
        ])->toArray());

        // User row
        $userDirty = false;
        if (array_key_exists('name', $data))  { $user->name  = $data['name'];  $userDirty = true; }
        if (array_key_exists('phone', $data)) { $user->phone = $data['phone']; $userDirty = true; }

        if ($request->hasFile('avatar')) {
            $user->replaceAvatar($request->file('avatar'));
            $userDirty = true;
        }

        if ($userDirty) {
            $user->save();
        }

        $doctor->load('user', 'specialization');
        return response()->json([
            'doctor' => (new DoctorResource($doctor))->resolve(),
        ]);
    }
}
