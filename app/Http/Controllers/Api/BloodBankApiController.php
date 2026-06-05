<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\BloodDonor;
use App\Models\BloodInventory;
use App\Models\BloodRequest;
use Illuminate\Http\Request;

class BloodBankApiController extends Controller
{
    public function inventory()
    {
        return response()->json([
            'data' => BloodInventory::orderBy('blood_group')->get(['blood_group', 'units']),
        ]);
    }

    public function donors(Request $request)
    {
        $bg = $request->query('blood_group');
        $list = BloodDonor::where('is_available', true)
            ->when($bg, fn ($q) => $q->where('blood_group', $bg))
            ->orderBy('city')
            ->limit(200)
            ->get(['id', 'name', 'blood_group', 'phone', 'city', 'last_donated_at']);

        return response()->json(['data' => $list]);
    }

    public function myRequests(Request $request)
    {
        $items = BloodRequest::where('user_id', $request->user()->id) ->latest()->get();
        return response()->json(['data' => $items]);
    }

    public function storeRequest(Request $request)
    {
        $data = $request->validate([
            'patient_name' => 'required|string|max:150',
            'blood_group' => 'required|string|max:5',
            'units' => 'required|integer|min:1|max:20',
            'hospital' => 'nullable|string|max:200',
            'contact_phone' => 'required|string|max:20',
            'needed_by' => 'required|date|after_or_equal:today',
            'reason' => 'nullable|string|max:500',
        ]);

        $req = BloodRequest::create(array_merge($data, [
            'user_id' => $request->user()->id,
            'status' => 'pending',
        ]));

        return response()->json(['request' => $req], 201);
    }

    public function registerDonor(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:150',
            'blood_group' => 'required|string|max:5',
            'phone' => 'required|string|max:20',
            'city' => 'required|string|max:100',
            'last_donated_at' => 'nullable|date|before_or_equal:today',
        ]);

        $donor = BloodDonor::updateOrCreate(
            ['user_id' => $request->user()->id],
            array_merge($data, ['is_available' => true]),
        );

        return response()->json(['donor' => $donor], 201);
    }
}
