<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\EmergencyRequest;
use App\Services\Notify;
use Illuminate\Http\Request;

class EmergencyController extends Controller
{
    public function index(Request $request)
    {
        $status = $request->status;
        $q = trim((string) $request->q);

        $requests = EmergencyRequest::with('user')
            ->when($status, fn($qr) => $qr->where('status', $status))
            ->when($q !== '', fn($qr) => $qr->where(function($w) use ($q) {
                $w->where('contact_name', 'like', "%{$q}%")
                  ->orWhere('contact_phone', 'like', "%{$q}%")
                  ->orWhere('location', 'like', "%{$q}%");
            }))
            ->latest()
            ->paginate(20)
            ->withQueryString();

        return view('admin.emergency.index', compact('requests', 'status', 'q'));
    }

    public function update(Request $request, EmergencyRequest $emergencyRequest)
    {
        $request->validate(['status' => 'required|in:pending,dispatched,arrived,resolved,cancelled']);
        $emergencyRequest->update(['status' => $request->status]);
        Notify::send($emergencyRequest->user_id, 'Emergency: '.ucfirst($request->status), 'Status updated to '.$request->status, null, 'ambulance');
        return back()->with('success','Status updated.');
    }
}
