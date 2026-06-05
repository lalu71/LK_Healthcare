<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function markAllRead(Request $request)
    {
        $request->user()->appNotifications()->whereNull('read_at')->update(['read_at' => now()]);
        return back();
    }
}
