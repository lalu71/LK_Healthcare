<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use Illuminate\Http\Request;

class DoctorAppointmentController extends Controller
{
    public function index(Request $request)
    {
        $doctor = $request->user()->doctor;
        abort_unless($doctor, 403);

        $query = Appointment::with('patient.user')->where('doctor_id', $doctor->id);
        if ($request->status) {
            $query->where('status', $request->status);
        }
        if ($request->date) {
            $query->whereDate('appointment_date', $request->date);
        }
        $appointments = $query->orderBy('appointment_date', 'desc')->paginate(12);
        return view('doctor.appointments.index', compact('appointments'));
    }

    public function show(Request $request, Appointment $appointment)
    {
        abort_unless($request->user()->doctor?->id === $appointment->doctor_id, 403);
        $appointment->load('patient.user','prescription.items');
        return view('doctor.appointments.show', compact('appointment'));
    }
}
