<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;

class SiteController extends Controller
{
    public function toggleShutdown(Request $request)
    {
        $next = ! Setting::isShutdown();
        Setting::setShutdown($next);

        return back()->with('success', $next
            ? __('Site is now in read-only mode. Non-admin actions are disabled.')
            : __('Site is now active. Everyone can perform actions.'));
    }
}
