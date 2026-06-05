<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\LabBooking;
use App\Models\LabTest;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class LabApiController extends Controller
{
    public function tests(Request $request)
    {
        $q = trim((string) $request->query('q', ''));
        $tests = LabTest::where('is_active', true)
            ->when($q, fn ($qr) => $qr->where('name', 'like', "%$q%"))
            ->orderBy('name')
            ->get(['id', 'name', 'category', 'description', 'price', 'duration_hours']);

        return response()->json(['data' => $tests]);
    }

    public function myBookings(Request $request)
    {
        $patient = $request->user()->patient;
        abort_unless($patient, 404, 'Patient profile not found');

        $bookings = LabBooking::with('labTest')
            ->where('patient_id', $patient->id)
            ->latest()
            ->get()
            ->map(fn ($b) => [
                'id' => $b->id,
                'booking_code' => $b->booking_code,
                'booking_date' => optional($b->booking_date)->toIso8601String(),
                'status' => $b->status,
                'payment_status' => $b->payment_status,
                'amount' => (float) $b->amount,
                'result_file' => $b->result_file,
                'notes' => $b->notes,
                'lab_test' => $b->labTest ? [
                    'id' => $b->labTest->id,
                    'name' => $b->labTest->name,
                    'category' => $b->labTest->category,
                    'price' => (float) $b->labTest->price,
                ] : null,
            ]);

        return response()->json(['data' => $bookings]);
    }

    public function book(Request $request)
    {
        $patient = $request->user()->patient;
        abort_unless($patient, 404, 'Patient profile not found');

        $data = $request->validate([
            'lab_test_id' => 'required|exists:lab_tests,id',
            'booking_date' => 'required|date|after:now',
            'notes' => 'nullable|string|max:500',
        ]);

        $test = LabTest::findOrFail($data['lab_test_id']);

        $booking = LabBooking::create([
            'patient_id' => $patient->id,
            'lab_test_id' => $test->id,
            'booking_code' => 'LAB-' . strtoupper(Str::random(8)),
            'booking_date' => $data['booking_date'],
            'status' => 'booked',
            'payment_status' => 'pending',
            'amount' => $test->price,
            'notes' => $data['notes'] ?? null,
        ]);

        return response()->json([
            'booking' => [
                'id' => $booking->id,
                'booking_code' => $booking->booking_code,
                'status' => $booking->status,
                'amount' => (float) $booking->amount,
            ],
        ], 201);
    }
}
