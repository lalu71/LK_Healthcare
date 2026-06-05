<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BloodDonor;
use App\Models\BloodInventory;
use App\Models\BloodRequest;
use Illuminate\Http\Request;

class BloodController extends Controller
{
    public function index()
    {
        $inventory = BloodInventory::orderBy('blood_group')->get();
        $requests = BloodRequest::with('user')->latest()->limit(15)->get();
        $donors = BloodDonor::latest()->limit(15)->get();
        return view('admin.blood.index', compact('inventory','requests','donors'));
    }

    public function updateInventory(Request $request)
    {
        $data = $request->validate([
            'blood_group' => 'required|string|max:5',
            'units' => 'required|integer|min:0',
        ]);
        BloodInventory::updateOrCreate(['blood_group'=>$data['blood_group']], ['units'=>$data['units']]);
        return back()->with('success','Inventory updated.');
    }

    public function updateRequest(Request $request, BloodRequest $bloodRequest)
    {
        $request->validate(['status' => 'required|in:pending,fulfilled,cancelled']);
        $bloodRequest->update(['status' => $request->status]);
        return back()->with('success','Request updated.');
    }
}
