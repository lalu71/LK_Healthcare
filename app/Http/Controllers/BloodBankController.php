<?php

namespace App\Http\Controllers;

use App\Models\BloodDonor;
use App\Models\BloodInventory;
use App\Models\BloodRequest;
use App\Services\Notify;
use Illuminate\Http\Request;

class BloodBankController extends Controller
{
    public function index(Request $request)
    {
        $inventory = BloodInventory::orderBy('blood_group')->get();
        $myRequests = collect();
        $donors = BloodDonor::where('is_available', true)->orderByDesc('created_at')->limit(8)->get();
        if ($request->user()) {
            $myRequests = BloodRequest::where('user_id', $request->user()->id)->latest()->limit(5)->get();
        }
        return view('blood.index', compact('inventory','myRequests','donors'));
    }

    public function request(Request $request)
    {
        $data = $request->validate([
            'patient_name' => 'required|string|max:120',
            'blood_group' => 'required|string|max:5',
            'units' => 'required|integer|min:1|max:10',
            'hospital' => 'nullable|string|max:150',
            'contact_phone' => 'required|string|max:20',
            'needed_by' => 'nullable|date|after_or_equal:today',
            'reason' => 'nullable|string|max:500',
        ]);
        $data['user_id'] = $request->user()->id;
        BloodRequest::create($data);
        Notify::admins('New blood request', $data['patient_name'].' · '.$data['blood_group'].' · '.$data['units'].' units', route('admin.blood.index'), 'droplet');
        return back()->with('success', 'Blood request submitted. Our team will contact you.');
    }

    public function registerDonor(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:120',
            'blood_group' => 'required|string|max:5',
            'phone' => 'required|string|max:20',
            'city' => 'nullable|string|max:80',
            'last_donated_at' => 'nullable|date|before_or_equal:today',
        ]);
        $data['user_id'] = $request->user()?->id;
        BloodDonor::create($data);
        return back()->with('success', 'Thank you for registering as a donor!');
    }
}
