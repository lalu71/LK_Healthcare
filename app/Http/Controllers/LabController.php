<?php

namespace App\Http\Controllers;

use App\Models\LabBooking;
use App\Models\LabTest;
use App\Services\Notify;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class LabController extends Controller
{
    public function index(Request $request)
    {
        $q = trim((string) $request->q);

        $tests = LabTest::where('is_active', true)
            ->when($q !== '', fn($query) => $query->where(fn($w) =>
                $w->where('name', 'like', "%{$q}%")->orWhere('category', 'like', "%{$q}%")
            ))
            ->orderBy('name')
            ->get();

        $patient = $request->user()->patient;
        $bookings = collect();
        if ($patient) {
            $bookings = LabBooking::with('labTest')->where('patient_id', $patient->id)->latest()->paginate(10);
        }
        return view('patient.lab.index', compact('tests','bookings','q'));
    }

    public function book(Request $request)
    {
        $patient = $request->user()->patient;
        if (!$patient) return redirect()->route('patient.profile.edit')->with('error','Complete your medical profile first.');

        $data = $request->validate([
            'lab_test_id' => 'required|exists:lab_tests,id',
            'booking_date' => 'required|date|after_or_equal:today',
        ]);
        $test = LabTest::findOrFail($data['lab_test_id']);

        $booking = LabBooking::create([
            'patient_id' => $patient->id,
            'lab_test_id' => $test->id,
            'booking_code' => 'LAB-'.strtoupper(Str::random(6)),
            'booking_date' => $data['booking_date'],
            'amount' => $test->price,
        ]);

        Notify::send($request->user()->id, 'Lab test booked', $test->name.' on '.$booking->booking_date->format('d M, h:i A'), route('patient.lab.index'), 'flask');

        return redirect()->route('payment.show', ['type' => 'lab', 'id' => $booking->id])
            ->with('success', 'Test booked. Complete payment to confirm.');
    }
}
