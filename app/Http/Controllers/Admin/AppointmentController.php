<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use Illuminate\Http\Request;

class AppointmentController extends Controller
{
    public function index(Request $request)
    {
        $query = Appointment::with('patient.user','doctor.user','doctor.specialization');
        if ($request->status) $query->where('status', $request->status);
        if ($request->date) $query->whereDate('appointment_date', $request->date);
        $appointments = $query->latest('appointment_date')->paginate(20)->withQueryString();
        return view('admin.appointments.index', compact('appointments'));
    }
}
