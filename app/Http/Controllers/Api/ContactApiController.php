<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Mail\ContactMessageMail;
use App\Models\ContactMessage;
use App\Services\Notify;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class ContactApiController extends Controller
{
    /**
     * Public contact form submission from the mobile app.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:100',
            'email' => 'required|email|max:120',
            'phone' => 'nullable|string|max:20',
            'subject' => 'nullable|string|max:120',
            'message' => 'required|string|max:3000',
        ]);

        $contact = ContactMessage::create($data);

        Notify::admins(
            'New contact message',
            $data['name'].': '.substr($data['message'], 0, 80),
            route('admin.contacts.index'),
            'mail'
        );

        try {
            Mail::to('lalje056@gmail.com')->send(new ContactMessageMail($data));
        } catch (\Exception $e) {
            Log::error('Failed to send contact email (API): '.$e->getMessage());
        }

        return response()->json([
            'success' => true,
            'message' => 'Thanks! We will get back to you within 24 hours.',
            'data' => $contact,
        ], 201);
    }
}
