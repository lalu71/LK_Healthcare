<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\MedicalRecord;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class MedicalRecordApiController extends Controller
{
    public function index(Request $request)
    {
        $patient = $request->user()->patient;
        abort_unless($patient, 404, 'Patient profile not found');

        $items = MedicalRecord::where('patient_id', $patient->id)
            ->latest()->get()
            ->map(fn ($r) => [
                'id' => $r->id,
                'title' => $r->title,
                'type' => $r->type,
                'description' => $r->description,
                'file_name' => $r->file_name,
                'file_size' => (int) $r->file_size,
                'record_date' => optional($r->record_date)->toDateString(),
                'created_at' => $r->created_at->toIso8601String(),
                'download_url' => $r->file_path ? Storage::disk('public')->url($r->file_path) : null,
            ]);

        return response()->json(['data' => $items]);
    }

    public function store(Request $request)
    {
        $patient = $request->user()->patient;
        abort_unless($patient, 404, 'Patient profile not found');

        $data = $request->validate([
            'title' => 'required|string|max:200',
            'type' => 'nullable|string|max:50',
            'description' => 'nullable|string|max:1000',
            'record_date' => 'nullable|date',
            'file' => 'required|file|max:10240', // 10 MB
        ]);

        $file = $request->file('file');
        $path = $file->store('medical-records', 'public');

        $rec = MedicalRecord::create([
            'patient_id' => $patient->id,
            'uploaded_by' => $request->user()->id,
            'title' => $data['title'],
            'type' => $data['type'] ?? 'other',
            'description' => $data['description'] ?? null,
            'record_date' => $data['record_date'] ?? now()->toDateString(),
            'file_path' => $path,
            'file_name' => $file->getClientOriginalName(),
            'file_size' => $file->getSize(),
        ]);

        return response()->json(['record' => $rec], 201);
    }

    public function destroy(Request $request, MedicalRecord $medicalRecord)
    {
        $patient = $request->user()->patient;
        abort_unless($patient && $medicalRecord->patient_id === $patient->id, 403);

        if ($medicalRecord->file_path) {
            Storage::disk('public')->delete($medicalRecord->file_path);
        }
        $medicalRecord->delete();
        return response()->json(['ok' => true]);
    }
}
