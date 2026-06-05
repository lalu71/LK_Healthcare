<?php

namespace App\Http\Controllers\Pharmacist;

use App\Http\Controllers\Controller;
use App\Models\Medicine;
use App\Models\Prescription;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'total_medicines' => Medicine::count(),
            'low_stock' => Medicine::where('stock', '<', 50)->count(),
            'pending_prescriptions' => Prescription::whereDoesntHave('appointment', function($q){
                $q->where('status', 'cancelled');
            })->count(), // Simplified for now
        ];

        $latest_prescriptions = Prescription::with('patient.user', 'doctor.user')
            ->latest()
            ->limit(5)
            ->get();

        return view('pharmacist.dashboard', compact('stats', 'latest_prescriptions'));
    }
}
