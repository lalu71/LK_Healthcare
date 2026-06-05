<?php

namespace App\Http\Controllers;

use App\Models\EmergencyRequest;
use App\Services\Notify;
use Illuminate\Http\Request;

class EmergencyController extends Controller
{
    public function create()
    {
        return view('emergency.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'contact_name' => 'required|string|max:120',
            'contact_phone' => 'required|string|max:20',
            'location' => 'required|string|max:255',
            'latitude' => 'nullable|string|max:32',
            'longitude' => 'nullable|string|max:32',
            'description' => 'nullable|string|max:1000',
        ]);
        $data['user_id'] = $request->user()?->id; // null for guests
        $er = EmergencyRequest::create($data);

        // Notify the requester only if they are logged in.
        if ($request->user()) {
            Notify::send($request->user()->id, '🚨 Emergency request sent', 'We are dispatching help. Reference #'.$er->id, route('emergency.create'), 'ambulance');
        }
        Notify::admins('🚨 EMERGENCY', $data['contact_name'].' · '.$data['location'], route('admin.emergency.index'), 'ambulance');

        return redirect()->route('emergency.create')->with('success', 'Emergency request sent! Our team will dispatch help immediately.');
    }
}
