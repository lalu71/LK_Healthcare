<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\DoctorResource;
use App\Models\Doctor;
use App\Models\Specialization;
use Illuminate\Http\Request;

class DoctorApiController extends Controller
{
    public function index(Request $request)
    {
        $q = trim((string) $request->query('q', ''));
        $specId = $request->query('specialization_id');

        $doctors = Doctor::with('user', 'specialization')
            ->where('is_active', true)
            ->whereHas('user', fn ($u) => $u->where('is_active', true))
            ->when($q, fn ($qr) => $qr->whereHas('user', fn ($u) => $u->where('name', 'like', "%$q%")))
            ->when($specId, fn ($qr) => $qr->where('specialization_id', $specId))
            ->orderBy('experience_years', 'desc')
            ->paginate(15);

        return DoctorResource::collection($doctors);
    }

    public function show(Doctor $doctor)
    {
        $doctor->load('user', 'specialization');
        return response()->json([
            'doctor' => (new DoctorResource($doctor))->resolve(),
        ]);
    }

    public function specializations()
    {
        return response()->json([
            'data' => Specialization::orderBy('name')->get(['id', 'name']),
        ]);
    }
}
