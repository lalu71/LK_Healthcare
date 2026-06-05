<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\AppointmentResource;
use App\Models\Appointment;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class AppointmentApiController extends Controller
{
    /** Patient: my appointments. */
    public function mine(Request $request)
    {
        $patient = $request->user()->patient;
        abort_unless($patient, 404, 'Patient profile not found');

        $query = Appointment::with('doctor.user', 'doctor.specialization')
            ->where('patient_id', $patient->id)
            ->orderBy('appointment_date', 'desc');

        if ($status = $request->query('status')) {
            $query->where('status', $status);
        }

        return AppointmentResource::collection($query->get());
    }

    /** All Appointment get Here */
    public function allAppointments(Request $request)
    {
        $doctor = auth()->user()->doctor;

        $appointments = Appointment::with(['doctor.user','patient.user'])
        ->where('doctor_id', $doctor->id)->whereIn('status', ['pending','confirmed','completed','cancelled'])
        ->orderBy('appointment_date', 'desc')
        ->get();

        return response()->json([
            'success' => true,
            'count' => $appointments->count(),
            'data' => AppointmentResource::collection($appointments)
        ]);
    }

    /** Doctor: appointments for me. */
    public function forDoctor(Request $request)
    {
        $doctor = $request->user()->doctor;
        abort_unless($doctor, 404, 'Doctor profile not found');

        $scope = $request->query('scope', 'today');

        $query = Appointment::with('patient.user', 'doctor.user')
            ->where('doctor_id', $doctor->id)
            ->orderBy('appointment_date', 'asc');

        if ($scope === 'today') {
            $query->whereDate('appointment_date', today());
        } elseif ($scope === 'upcoming') {
            $query->where('appointment_date', '>=', now())->whereNotIn('status', ['cancelled', 'completed']);
        }

        return AppointmentResource::collection($query->get());
    }

    public function store(Request $request)
    {
        $patient = $request->user()->patient;
        abort_unless($patient, 404, 'Patient profile not found');

        $data = $request->validate([
            'doctor_id' => 'required|exists:doctors,id',
            'appointment_date' => 'required|date|after:now',
            'reason' => 'nullable|string|max:500',
        ]);

        $apt = Appointment::create([
            'patient_id' => $patient->id,
            'doctor_id' => $data['doctor_id'],
            'appointment_date' => $data['appointment_date'],
            'reason' => $data['reason'] ?? null,
            'status' => 'pending',
            'payment_status' => 'pending',
        ]);

        $apt->load('doctor.user', 'doctor.specialization');
        return response()->json([
            'appointment' => (new AppointmentResource($apt))->resolve(),
        ], 201);
    }

    public function cancel(Request $request, Appointment $appointment)
    {
        // Patient can only cancel own appointment.
        $patient = $request->user()->patient;
        abort_unless($patient && $appointment->patient_id === $patient->id, 403);

        $appointment->update(['status' => 'cancelled']);
        $appointment->load('doctor.user');
        return response()->json([
            'appointment' => (new AppointmentResource($appointment))->resolve(),
        ]);
    }

    public function updateStatus(Request $request, Appointment $appointment)
    {
        // Doctor can update own appointments' status.
        $doctor = $request->user()->doctor;
        abort_unless($doctor && $appointment->doctor_id === $doctor->id, 403);

        $data = $request->validate([
            'status' => ['required', Rule::in(['confirmed', 'completed', 'cancelled'])],
        ]);

        $appointment->update(['status' => $data['status']]);
        $appointment->load('patient.user');
        return response()->json([
            'appointment' => (new AppointmentResource($appointment))->resolve(),
        ]);
    }
}
