<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\LabBooking;
use App\Models\LabTest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class LabController extends Controller
{
    public function index(Request $request)
    {
        $q = trim((string) $request->q);

        $tests = LabTest::query()
            ->when($q !== '', fn($query) => $query->where(fn($w) =>
                $w->where('name', 'like', "%{$q}%")->orWhere('category', 'like', "%{$q}%")
            ))
            ->orderBy('name')
            ->paginate(15)
            ->withQueryString();

        $bookings = LabBooking::with('patient.user','labTest')->latest()->limit(10)->get();
        return view('admin.lab.index', compact('tests','bookings','q'));
    }

    public function storeTest(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:150',
            'category' => 'nullable|string|max:80',
            'price' => 'required|numeric|min:0',
            'duration_hours' => 'required|integer|min:1',
            'description' => 'nullable|string|max:1000',
        ]);
        LabTest::create($data + ['is_active' => true]);
        return back()->with('success','Test added.');
    }

    public function toggleTest(LabTest $labTest)
    {
        $labTest->update(['is_active' => !$labTest->is_active]);
        return back();
    }

    public function uploadResult(Request $request, LabBooking $booking)
    {
        $request->validate(['file' => 'required|file|mimes:pdf,jpg,jpeg,png|max:10240']);
        $path = $request->file('file')->store('lab-results', 'public');
        $booking->update(['result_file' => $path, 'status' => 'reported']);
        return back()->with('success','Result uploaded.');
    }
}
