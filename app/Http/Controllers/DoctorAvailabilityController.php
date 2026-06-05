<?php

namespace App\Http\Controllers;

use App\Models\Availability;
use Illuminate\Http\Request;

class DoctorAvailabilityController extends Controller
{
    public function index(Request $request)
    {
        $doctor = $request->user()->doctor;
        abort_unless($doctor, 403);
        $availabilities = $doctor->availabilities()->orderBy('day_of_week')->get();
        return view('doctor.availability.index', compact('availabilities'));
    }

    public function store(Request $request)
    {
        $doctor = $request->user()->doctor;
        abort_unless($doctor, 403);

        $data = $request->validate([
            'day_of_week' => 'required|integer|between:0,6',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'slot_minutes' => 'required|integer|in:15,20,30,45,60',
        ]);

        if ($doctor->availabilities()->where('day_of_week', $data['day_of_week'])->exists()) {
            return back()->withErrors(['day_of_week' => __('Availability for this day already exists. Please update it instead.')])->withInput();
        }

        $data['doctor_id'] = $doctor->id;
        $data['is_active'] = true;
        Availability::create($data);
        return back()->with('success', 'Availability added.');
    }

    public function update(Request $request, Availability $availability)
    {
        abort_unless($request->user()->doctor?->id === $availability->doctor_id, 403);

        $data = $request->validate([
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'slot_minutes' => 'required|integer|in:15,20,30,45,60',
        ]);

        $availability->update($data);
        return back()->with('success', 'Availability updated.');
    }

    public function destroy(Request $request, Availability $availability)
    {
        abort_unless($request->user()->doctor?->id === $availability->doctor_id, 403);
        $availability->delete();
        return back()->with('success', 'Slot removed.');
    }
}
