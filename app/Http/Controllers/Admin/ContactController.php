<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ContactMessage;
use Illuminate\Http\Request;

class ContactController extends Controller
{
    public function index(Request $request)
    {
        $q = trim((string) $request->q);
        $status = $request->status; // 'handled' | 'pending' | ''

        $messages = ContactMessage::query()
            ->when($q !== '', fn($qr) => $qr->where(function($w) use ($q) {
                $w->where('name', 'like', "%{$q}%")
                  ->orWhere('email', 'like', "%{$q}%")
                  ->orWhere('subject', 'like', "%{$q}%")
                  ->orWhere('message', 'like', "%{$q}%");
            }))
            ->when($status === 'handled', fn($qr) => $qr->where('is_handled', true))
            ->when($status === 'pending', fn($qr) => $qr->where('is_handled', false))
            ->latest()
            ->paginate(20)
            ->withQueryString();

        return view('admin.contacts.index', compact('messages', 'q', 'status'));
    }

    public function toggle(ContactMessage $message)
    {
        $message->update(['is_handled' => !$message->is_handled]);
        return back();
    }

    public function reply(Request $request, ContactMessage $message)
    {
        $request->validate([
            'reply_message' => 'required|string',
        ]);

        try {
            \Illuminate\Support\Facades\Mail::to($message->email)
                ->send(new \App\Mail\ContactReplyMail($request->reply_message, $message->subject ?? ''));
            
            $message->update(['is_handled' => true]);
            
            return back()->with('success', 'Reply sent successfully!');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to send reply: ' . $e->getMessage());
        }
    }

    public function destroy(ContactMessage $message)
    {
        $message->delete();
        return back()->with('success', 'Message deleted successfully!');
    }

    public function destroyAll()
    {
        ContactMessage::truncate();
        return back()->with('success', 'All messages have been deleted!');
    }
}
