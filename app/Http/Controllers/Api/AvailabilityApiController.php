<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Models\Availability;
use App\Models\Doctor;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class AvailabilityApiController extends Controller
{
    /** Doctor: list my availability rows. */
    public function mine(Request $request)
    {
        $doctor = $request->user()->doctor;
        abort_unless($doctor, 404, 'Doctor profile not found');

        return response()->json([
            'data' => $doctor->availabilities()->orderBy('day_of_week')->orderBy('start_time')->get()->map(fn ($a) => [
                'id' => $a->id,
                'day_of_week' => (int) $a->day_of_week,
                'day_name' => Availability::dayName((int) $a->day_of_week),
                'start_time' => $a->start_time,
                'end_time' => $a->end_time,
                'slot_minutes' => (int) $a->slot_minutes,
                'is_active' => (bool) $a->is_active,
            ]),
        ]);
    }

    public function store(Request $request)
    {
        $doctor = $request->user()->doctor;
        abort_unless($doctor, 404, 'Doctor profile not found');

        $data = $request->validate([
            'day_of_week' => 'required|integer|min:0|max:6',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'slot_minutes' => 'nullable|integer|min:5|max:240',
        ]);

        if ($doctor->availabilities()->where('day_of_week', $data['day_of_week'])->exists()) {
            return response()->json([
                'message' => 'Availability for this day already exists. Please update it instead.',
                'errors' => ['day_of_week' => ['Availability for this day already exists. Please update it instead.']],
            ], 422);
        }

        $av = $doctor->availabilities()->create([
            'day_of_week' => $data['day_of_week'],
            'start_time' => $data['start_time'],
            'end_time' => $data['end_time'],
            'slot_minutes' => $data['slot_minutes'] ?? 30,
            'is_active' => true,
        ]);

        return response()->json(['id' => $av->id, 'ok' => true], 201);
    }

    public function update(Request $request, Availability $availability)
    {
        $doctor = $request->user()->doctor;
        abort_unless($doctor && $availability->doctor_id === $doctor->id, 403);

        $data = $request->validate([
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'slot_minutes' => 'nullable|integer|min:5|max:240',
        ]);

        $availability->update([
            'start_time' => $data['start_time'],
            'end_time' => $data['end_time'],
            'slot_minutes' => $data['slot_minutes'] ?? $availability->slot_minutes,
        ]);

        return response()->json([
            'ok' => true,
            'data' => [
                'id' => $availability->id,
                'day_of_week' => (int) $availability->day_of_week,
                'day_name' => Availability::dayName((int) $availability->day_of_week),
                'start_time' => $availability->start_time,
                'end_time' => $availability->end_time,
                'slot_minutes' => (int) $availability->slot_minutes,
                'is_active' => (bool) $availability->is_active,
            ],
        ]);
    }

    public function destroy(Request $request, Availability $availability)
    {
        $doctor = $request->user()->doctor;
        abort_unless($doctor && $availability->doctor_id === $doctor->id, 403);
        $availability->delete();
        return response()->json(['ok' => true]);
    }

    /** Patient: get bookable slots for a doctor on a date. */
    public function slots(Request $request, Doctor $doctor)
    {
        $date = $request->query('date');
        $request->validate(['date' => 'required|date_format:Y-m-d']);

        $day = Carbon::parse($date);
        $dow = (int) $day->dayOfWeek;

        $rows = $doctor->availabilities()
            ->where('day_of_week', $dow)
            ->where('is_active', true)
            ->get();

        $taken = Appointment::where('doctor_id', $doctor->id)
            ->whereDate('appointment_date', $day->toDateString())
            ->whereNotIn('status', ['cancelled'])
            ->pluck('appointment_date')
            ->map(fn ($d) => Carbon::parse($d)->format('H:i'))
            ->all();

        $slots = [];
        foreach ($rows as $row) {
            $start = Carbon::parse($day->toDateString() . ' ' . $row->start_time);
            $end = Carbon::parse($day->toDateString() . ' ' . $row->end_time);
            $cursor = $start->copy();
            while ($cursor->lt($end)) {
                $time = $cursor->format('H:i');
                $isPast = $day->isToday() && $cursor->lt(now());
                if (! in_array($time, $taken, true) && ! $isPast) {
                    $slots[] = [
                        'time' => $time,
                        'iso' => $cursor->toIso8601String(),
                    ];
                }
                $cursor->addMinutes((int) $row->slot_minutes);
            }
        }

        return response()->json(['date' => $day->toDateString(), 'slots' => $slots]);
    }
}
