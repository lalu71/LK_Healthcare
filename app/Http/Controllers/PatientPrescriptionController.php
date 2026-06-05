<?php

namespace App\Http\Controllers;

use App\Models\Prescription;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class PatientPrescriptionController extends Controller
{
    public function index(Request $request)
    {
        $patient = $request->user()->patient;
        abort_unless($patient, 403);

        $q = trim((string) $request->q);

        $prescriptions = Prescription::with('doctor.user','doctor.specialization','items')
            ->where('patient_id', $patient->id)
            ->when($q !== '', fn($query) => $query->where(fn($w) =>
                $w->where('prescription_code', 'like', "%{$q}%")
                  ->orWhere('diagnosis', 'like', "%{$q}%")
                  ->orWhereHas('doctor.user', fn($u) => $u->where('name', 'like', "%{$q}%"))
            ))
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('patient.prescriptions.index', compact('prescriptions', 'q'));
    }

    public function show(Request $request, Prescription $prescription)
    {
        abort_unless($request->user()->patient?->id === $prescription->patient_id, 403);
        $prescription->load('doctor.user','doctor.specialization','patient.user','items');
        return view('patient.prescriptions.show', compact('prescription'));
    }

    public function pdf(Request $request, Prescription $prescription)
    {
        abort_unless($request->user()->patient?->id === $prescription->patient_id, 403);
        $prescription->load('doctor.user','doctor.specialization','patient.user','items');
        $pdf = Pdf::loadView('pdf.prescription', compact('prescription'));
        return $pdf->download('prescription-'.$prescription->prescription_code.'.pdf');
    }
}
