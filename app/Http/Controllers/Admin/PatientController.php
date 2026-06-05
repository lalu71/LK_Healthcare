<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Patient;
use Illuminate\Http\Request;

class PatientController extends Controller
{
    public function index(Request $request)
    {
        $q = trim((string) $request->q);
        $patients = Patient::with('user')
            ->when($q, function($qr) use ($q) {
                $qr->whereHas('user', fn($u)=>$u->where('name','like',"%$q%")->orWhere('email','like',"%$q%"))
                   ->orWhere('patient_id','like',"%$q%")
                   ->orWhere('aadhaar_number','like',"%$q%");
            })
            ->latest()->paginate(10)->withQueryString();
        return view('admin.patients.index', compact('patients','q'));
    }

    public function show(Patient $patient)
    {
        $patient->load('user','appointments.doctor.user','prescriptions.doctor.user','medicalRecords','labBookings.labTest');
        return view('admin.patients.show', compact('patient'));
    }

    public function toggleActive(Patient $patient)
    {
        $patient->user->update(['is_active' => ! $patient->user->is_active]);
        return back()->with('success', $patient->user->is_active
            ? __('Patient activated.')
            : __('Patient deactivated. They will not be able to log in.'));
    }
}
