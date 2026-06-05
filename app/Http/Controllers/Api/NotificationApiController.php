<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AppNotification;
use Illuminate\Http\Request;

class NotificationApiController extends Controller
{
    public function index(Request $request)
    {
        $items = AppNotification::where('user_id', $request->user()->id)
            ->latest()->limit(100)->get()
            ->map(fn ($n) => [
                'id' => $n->id,
                'title' => $n->title,
                'body' => $n->body,
                'icon' => $n->icon,
                'link' => $n->link,
                'read_at' => optional($n->read_at)->toIso8601String(),
                'created_at' => $n->created_at->toIso8601String(),
            ]);

        $unread = AppNotification::where('user_id', $request->user()->id)
            ->whereNull('read_at')->count();

        return response()->json(['data' => $items, 'unread_count' => $unread]);
    }

    public function readAll(Request $request)
    {
        AppNotification::where('user_id', $request->user()->id)
            ->whereNull('read_at')
            ->update(['read_at' => now()]);
        return response()->json(['ok' => true]);
    }
}
