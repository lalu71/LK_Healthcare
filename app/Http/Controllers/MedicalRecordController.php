<?php

namespace App\Http\Controllers;

use App\Models\MedicalRecord;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class MedicalRecordController extends Controller
{
    public function index(Request $request)
    {
        $patient = $request->user()->patient;
        abort_unless($patient, 403);

        $q = trim((string) $request->q);
        $type = $request->type;

        $records = MedicalRecord::where('patient_id', $patient->id)
            ->when($q !== '', fn($query) => $query->where(fn($w) =>
                $w->where('title', 'like', "%{$q}%")->orWhere('description', 'like', "%{$q}%")
            ))
            ->when($type, fn($query) => $query->where('type', $type))
            ->latest()
            ->paginate(12)
            ->withQueryString();

        return view('patient.records.index', compact('records', 'q', 'type'));
    }

    public function store(Request $request)
    {
        $patient = $request->user()->patient;
        abort_unless($patient, 403);

        $data = $request->validate([
            'title' => 'required|string|max:160',
            'type' => 'required|in:lab,xray,mri,prescription,other',
            'description' => 'nullable|string|max:1000',
            'record_date' => 'nullable|date',
            'file' => 'required|file|mimes:pdf,jpg,jpeg,png,webp|max:10240',
        ]);

        $file = $request->file('file');
        $path = $file->store('medical-records/'.$patient->id, 'public');

        MedicalRecord::create([
            'patient_id' => $patient->id,
            'uploaded_by' => $request->user()->id,
            'title' => $data['title'],
            'type' => $data['type'],
            'description' => $data['description'] ?? null,
            'record_date' => $data['record_date'] ?? null,
            'file_path' => $path,
            'file_name' => $file->getClientOriginalName(),
            'file_size' => $file->getSize(),
        ]);

        return back()->with('success', 'Record uploaded successfully.');
    }

    public function download(Request $request, MedicalRecord $record)
    {
        $user = $request->user();
        $isOwner = $user->patient?->id === $record->patient_id;
        $isDoctor = $user->hasRole('doctor');
        $isAdmin = $user->hasRole('admin');
        abort_unless($isOwner || $isDoctor || $isAdmin, 403);

        return Storage::disk('public')->download($record->file_path, $record->file_name);
    }

    public function destroy(Request $request, MedicalRecord $record)
    {
        abort_unless($request->user()->patient?->id === $record->patient_id, 403);
        Storage::disk('public')->delete($record->file_path);
        $record->delete();
        return back()->with('success', 'Record deleted.');
    }
}
