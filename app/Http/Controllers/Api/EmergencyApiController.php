<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\EmergencyRequest;
use Illuminate\Http\Request;

class EmergencyApiController extends Controller
{
    public function mine(Request $request)
    {
        $items = EmergencyRequest::where('user_id', $request->user()->id)
            ->latest()->get();
        return response()->json(['data' => $items]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'contact_name' => 'required|string|max:150',
            'contact_phone' => 'required|string|max:20',
            'location' => 'required|string|max:500',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'description' => 'nullable|string|max:1000',
        ]);

        $req = EmergencyRequest::create(array_merge($data, [
            'user_id' => $request->user()->id,
            'status' => 'pending',
        ]));

        return response()->json(['request' => $req], 201);
    }
}
