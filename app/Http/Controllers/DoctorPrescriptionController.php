<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\Medicine;
use App\Models\Prescription;
use App\Models\PrescriptionItem;
use App\Services\Notify;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class DoctorPrescriptionController extends Controller
{
    public function index(Request $request)
    {
        $doctor = $request->user()->doctor;
        abort_unless($doctor, 403);

        $q = trim((string) $request->q);

        $prescriptions = Prescription::with('patient.user','items','appointment')
            ->where('doctor_id', $doctor->id)
            ->when($q !== '', fn($query) => $query->where(fn($w) =>
                $w->where('prescription_code', 'like', "%{$q}%")
                  ->orWhere('diagnosis', 'like', "%{$q}%")
                  ->orWhereHas('patient.user', fn($u) => $u->where('name', 'like', "%{$q}%"))
            ))
            ->latest()
            ->paginate(12)
            ->withQueryString();

        return view('doctor.prescriptions.index', compact('prescriptions', 'q'));
    }

    public function create(Request $request)
    {
        $doctor = $request->user()->doctor;
        abort_unless($doctor, 403);

        $appointment = null;
        if ($request->appointment_id) {
            $appointment = Appointment::with('patient.user')->findOrFail($request->appointment_id);
            abort_unless($appointment->doctor_id === $doctor->id, 403);
        }
        $medicines = Medicine::where('is_active', true)->orderBy('name')->get();
        return view('doctor.prescriptions.create', compact('appointment', 'medicines'));
    }

    public function store(Request $request)
    {
        $doctor = $request->user()->doctor;
        abort_unless($doctor, 403);

        $data = $request->validate([
            'appointment_id' => 'required|exists:appointments,id',
            'diagnosis' => 'nullable|string|max:2000',
            'advice' => 'nullable|string|max:2000',
            'follow_up_date' => 'nullable|date|after_or_equal:today',
            'items' => 'required|array|min:1',
            'items.*.medicine_id' => 'nullable|exists:medicines,id',
            'items.*.medicine_name' => 'required|string|max:120',
            'items.*.dosage' => 'required|string|max:60',
            'items.*.frequency' => 'required|string|max:60',
            'items.*.duration' => 'required|string|max:60',
            'items.*.instructions' => 'nullable|string|max:255',
        ]);

        $appointment = Appointment::with('patient.user')->findOrFail($data['appointment_id']);
        abort_unless($appointment->doctor_id === $doctor->id, 403);

        $prescription = Prescription::create([
            'appointment_id' => $appointment->id,
            'patient_id' => $appointment->patient_id,
            'doctor_id' => $doctor->id,
            'prescription_code' => 'RX-'.strtoupper(Str::random(8)),
            'diagnosis' => $data['diagnosis'] ?? null,
            'advice' => $data['advice'] ?? null,
            'follow_up_date' => $data['follow_up_date'] ?? null,
        ]);
        foreach ($data['items'] as $item) {
            PrescriptionItem::create(array_merge($item, ['prescription_id' => $prescription->id]));
        }

        $appointment->update(['status' => 'completed']);

        if ($appointment->patient?->user_id) {
            Notify::send($appointment->patient->user_id,
                'New prescription',
                'Dr. '.$request->user()->name.' issued a prescription',
                route('patient.prescriptions.show', $prescription->id),
                'pill'
            );
        }

        return redirect()->route('doctor.prescriptions.index')->with('success', 'Prescription created.');
    }

    public function show(Request $request, Prescription $prescription)
    {
        abort_unless($request->user()->doctor?->id === $prescription->doctor_id, 403);
        $prescription->load('patient.user','items','appointment');
        return view('doctor.prescriptions.show', compact('prescription'));
    }
}
