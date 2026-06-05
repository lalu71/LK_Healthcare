<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Appointment;
use App\Models\Doctor;
use App\Models\Availability;
use App\Models\Specialization;
use App\Services\Notify;
use Carbon\Carbon;

class AppointmentController extends Controller
{
    public function create(Request $request)
    {
        $patient = $request->user()->patient;
        if (!$patient) {
            return redirect()->route('patient.profile.edit')->with('error', 'Please complete your medical profile to book an appointment.');
        }

        $query = Doctor::with(['user', 'specialization', 'availabilities'])->where('is_active', true);
        if ($request->specialization) {
            $query->where('specialization_id', $request->specialization);
        }
        if ($request->q) {
            $query->whereHas('user', fn($q) => $q->where('name', 'like', '%'.$request->q.'%'));
        }
        $doctors = $query->get();
        $specializations = Specialization::all();

        $selectedDoctor = null;
        if ($request->doctor_id) {
            $selectedDoctor = Doctor::with(['user','specialization','availabilities'])->find($request->doctor_id);
        }

        return view('patient.book-appointment', compact('doctors', 'specializations', 'selectedDoctor'));
    }

    public function slots(Request $request, Doctor $doctor)
    {
        $request->validate(['date' => 'required|date|after_or_equal:today']);
        $date = Carbon::parse($request->date);
        $dow = (int) $date->dayOfWeek;

        $availabilities = $doctor->availabilities()->where('day_of_week', $dow)->where('is_active', true)->get();
        $booked = Appointment::where('doctor_id', $doctor->id)
            ->whereDate('appointment_date', $date->toDateString())
            ->whereIn('status', ['pending','confirmed'])
            ->pluck('appointment_date')
            ->map(fn($d) => Carbon::parse($d)->format('H:i'))
            ->toArray();

        $slots = [];
        foreach ($availabilities as $a) {
            $start = Carbon::parse($date->toDateString().' '.$a->start_time);
            $end = Carbon::parse($date->toDateString().' '.$a->end_time);
            while ($start->lt($end)) {
                $slot = $start->copy();
                if ($slot->isPast()) { $start->addMinutes($a->slot_minutes); continue; }
                $slots[] = [
                    'time' => $slot->format('H:i'),
                    'label' => $slot->format('h:i A'),
                    'taken' => in_array($slot->format('H:i'), $booked),
                ];
                $start->addMinutes($a->slot_minutes);
            }
        }
        return response()->json(['slots' => $slots]);
    }

    public function store(Request $request)
    {
        $patient = $request->user()->patient;
        if (!$patient) {
            return redirect()->route('patient.profile.edit')->with('error', 'Please complete your medical profile first.');
        }

        $validated = $request->validate([
            'doctor_id' => 'required|exists:doctors,id',
            'appointment_date' => 'required|date',
            'slot' => 'required|date_format:H:i',
            'reason' => 'nullable|string|max:1000',
        ]);

        $when = Carbon::parse($validated['appointment_date'].' '.$validated['slot']);
        if ($when->isPast()) {
            return back()->with('error', 'Selected slot is in the past.');
        }

        $conflict = Appointment::where('doctor_id', $validated['doctor_id'])
            ->where('appointment_date', $when)
            ->whereIn('status', ['pending','confirmed'])
            ->exists();

        if ($conflict) {
            return back()->with('error', 'This slot has just been booked. Pick another slot.');
        }

        $appointment = Appointment::create([
            'patient_id' => $patient->id,
            'doctor_id' => $validated['doctor_id'],
            'appointment_date' => $when,
            'reason' => $validated['reason'] ?? null,
            'status' => 'pending',
            'payment_status' => 'unpaid',
        ]);

        $doctor = Doctor::with('user')->find($validated['doctor_id']);
        Notify::send($request->user()->id,
            'Appointment booked',
            'With Dr. '.$doctor->user->name.' on '.$when->format('d M, h:i A'),
            route('patient.appointments.index'),
            'calendar'
        );
        Notify::send($doctor->user->id,
            'New appointment',
            $request->user()->name.' booked for '.$when->format('d M, h:i A'),
            route('doctor.appointments.index'),
            'calendar'
        );

        return redirect()->route('payment.show', ['type' => 'appointment', 'id' => $appointment->id])
            ->with('success', 'Appointment booked! Please complete payment.');
    }

    public function index(Request $request)
    {
        $patient = $request->user()->patient;
        if (!$patient) {
            return redirect()->route('dashboard');
        }

        $q = trim((string) $request->q);
        $status = $request->status;

        $appointments = Appointment::with('doctor.user','doctor.specialization','prescription')
            ->where('patient_id', $patient->id)
            ->when($q !== '', fn($query) => $query->whereHas('doctor.user', fn($u) => $u->where('name', 'like', "%{$q}%")))
            ->when($status, fn($query) => $query->where('status', $status))
            ->orderBy('appointment_date', 'desc')
            ->paginate(10)
            ->withQueryString();

        return view('patient.appointments.index', compact('appointments', 'q', 'status'));
    }

    public function destroy(Appointment $appointment)
    {
        if (auth()->user()->patient?->id !== $appointment->patient_id) {
            abort(403);
        }
        if (!in_array($appointment->status, ['pending','confirmed'])) {
            return back()->with('error', 'This appointment cannot be cancelled.');
        }
        if ($appointment->appointment_date->isPast()) {
            return back()->with('error', 'Past appointments cannot be cancelled.');
        }
        $appointment->update(['status' => 'cancelled']);
        return back()->with('success', 'Appointment cancelled successfully.');
    }

    public function updateStatus(Request $request, Appointment $appointment)
    {
        if (auth()->user()->doctor?->id !== $appointment->doctor_id) {
            abort(403);
        }
        $request->validate([
            'status' => 'required|in:confirmed,completed,cancelled',
            'doctor_notes' => 'nullable|string|max:2000',
        ]);
        $appointment->update([
            'status' => $request->status,
            'doctor_notes' => $request->doctor_notes ?? $appointment->doctor_notes,
        ]);

        $patientUserId = $appointment->patient->user_id ?? null;
        if ($patientUserId) {
            Notify::send($patientUserId,
                'Appointment '.$request->status,
                'Your appointment with Dr. '.auth()->user()->name.' is now '.$request->status,
                route('patient.appointments.index'),
                'calendar'
            );
        }

        return back()->with('success', 'Appointment updated.');
    }
}
