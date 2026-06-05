<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\PrescriptionResource;
use App\Models\Prescription;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class PrescriptionApiController extends Controller
{
    /** Patient: my prescriptions. */
    public function mine(Request $request)
    {
        $patient = $request->user()->patient;
        abort_unless($patient, 404, 'Patient profile not found');

        $items = Prescription::with('doctor.user', 'patient.user', 'items')
            ->where('patient_id', $patient->id)
            ->latest()->get();

        return PrescriptionResource::collection($items);
    }

    /** Doctor: prescriptions I've issued. */
    public function issued(Request $request)
    {
        $doctor = $request->user()->doctor;
        abort_unless($doctor, 404, 'Doctor profile not found');

        $items = Prescription::with('doctor.user', 'patient.user', 'items')
            ->where('doctor_id', $doctor->id)
            ->latest()->get();

        return PrescriptionResource::collection($items);
    }

    public function show(Request $request, Prescription $prescription)
    {
        $user = $request->user();
        // Patient owns or doctor issued can view.
        $isOwner = $user->patient && $prescription->patient_id === $user->patient->id;
        $isIssuer = $user->doctor && $prescription->doctor_id === $user->doctor->id;
        abort_unless($isOwner || $isIssuer || $user->hasRole(['admin', 'pharmacist']), 403);

        $prescription->load('doctor.user', 'patient.user', 'items');
        return response()->json([
            'prescription' => (new PrescriptionResource($prescription))->resolve(),
        ]);
    }

    public function store(Request $request)
    {
        $doctor = $request->user()->doctor;
        abort_unless($doctor, 403, 'Only doctors can create prescriptions');

        $data = $request->validate([
            'appointment_id' => 'required|exists:appointments,id',
            'diagnosis' => 'nullable|string',
            'advice' => 'nullable|string',
            'follow_up_date' => 'nullable|date',
            'items' => 'required|array|min:1',
            'items.*.medicine_name' => 'required|string|max:150',
            'items.*.dosage' => 'required|string|max:80',
            'items.*.frequency' => 'required|string|max:80',
            'items.*.duration' => 'required|string|max:80',
            'items.*.instructions' => 'nullable|string|max:200',
        ]);

        $appointment = \App\Models\Appointment::findOrFail($data['appointment_id']);
        abort_unless($appointment->doctor_id === $doctor->id, 403);

        $prescription = Prescription::create([
            'appointment_id' => $appointment->id,
            'patient_id' => $appointment->patient_id,
            'doctor_id' => $doctor->id,
            'prescription_code' => 'RX-' . strtoupper(Str::random(8)),
            'diagnosis' => $data['diagnosis'] ?? null,
            'advice' => $data['advice'] ?? null,
            'follow_up_date' => $data['follow_up_date'] ?? null,
            'status' => 'pending',
        ]);
        foreach ($data['items'] as $item) {
            $prescription->items()->create($item);
        }

        $prescription->load('doctor.user', 'patient.user', 'items');
        return response()->json([
            'prescription' => (new PrescriptionResource($prescription))->resolve(),
        ], 201);
    }
}
