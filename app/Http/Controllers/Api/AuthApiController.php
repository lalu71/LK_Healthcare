<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Models\Patient;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Spatie\Permission\Models\Role;

class AuthApiController extends Controller
{
    public function login(Request $request)
    {
        $data = $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
            'device_name' => 'nullable|string|max:60',
        ]);

        $user = User::where('email', $data['email'])->first();

        if (! $user || ! Hash::check($data['password'], $user->password)) {
            throw ValidationException::withMessages([
                'email' => [__('The provided credentials are incorrect.')],
            ]);
        }
        if (! $user->is_active) {
            throw ValidationException::withMessages([
                'email' => [__('Your account has been deactivated. Please contact the administrator.')],
            ]);
        }

        $token = $user->createToken($data['device_name'] ?? 'mobile')->plainTextToken;

        return response()->json([
            'status' => true,
            'message' => 'Login Success',
            'data' => [
                'token' => $token,
                'user' => (new UserResource($user))->resolve(),
            ]
        ], 200);
    }

    public function register(Request $request)
    {
        $patientId = '';
        $data = $request->validate([
            'name' => 'required|string|max:150',
            'email' => 'required|email|unique:users,email',
            'phone' => 'nullable|string|max:20',
            'password' => 'required|string|min:6|confirmed',
            'avatar' => 'nullable|image|mimes:jpeg,jpg,png,webp|max:2048',
        ]);

        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'phone' => $data['phone'] ?? null,
            'password' => Hash::make($data['password']),
            'is_active' => true,
        ]);

        // Now that $user->id exists, save avatar under avatars/{id}/{hash}.ext
        if ($request->hasFile('avatar')) {
            $user->avatar = $user->storeAvatar($request->file('avatar'));
            $user->save();
        }
        $role = Role::firstOrCreate(['name' => 'patient']);
        $user->assignRole($role);

        // Create the linked Patient profile.
        do {
            $patientId = 'LK-' . random_int(1000000, 9999999);
        } while (\App\Models\Patient::where('patient_id', $patientId)->exists());

        Patient::firstOrCreate(
         ['user_id' => $user->id],
            [
                'patient_id' => $patientId,
            ]   
        );

        $token = $user->createToken('mobile')->plainTextToken;

        return response()->json([
            'status' => true,
            'message' => 'Registration Success',
            'data' => [
                'token' => $token,
                'user' => (new UserResource($user))->resolve(),
            ]
        ], 201);
    }

    public function me(Request $request)
    {
        return response()->json([
            'user' => (new UserResource($request->user()))->resolve(),
        ]);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
        return response()->json(['ok' => true]);
    }
}
