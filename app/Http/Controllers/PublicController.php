<?php

namespace App\Http\Controllers;

use App\Models\ContactMessage;
use App\Models\Doctor;
use App\Models\Specialization;
use App\Services\Notify;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Mail\ContactMessageMail;

class PublicController extends Controller
{
    public function home()
    {
        $siteContent = \App\Models\SiteContent::first();
        $doctorsCount = Doctor::where('is_active', true)->count();
        $specialitiesCount = Specialization::count();
        $usersCount = \App\Models\User::count();
        $reviews = \App\Models\Review::with('user')->where('is_approved', true)->latest()->get();
        return view('welcome', compact('siteContent', 'doctorsCount', 'specialitiesCount', 'usersCount', 'reviews'));
    }

    public function about()
    {
        return view('public.about');
    }

    public function services()
    {
        $services = \App\Models\Service::where('status', 1)->latest()->get();
        return view('public.services', compact('services'));
    }

    public function doctors(Request $request)
    {
        $query = Doctor::with(['user','specialization'])->where('is_active', true);
        if ($request->specialization) {
            $query->where('specialization_id', $request->specialization);
        }
        $doctors = $query->get();
        $specializations = Specialization::all();
        return view('public.doctors', compact('doctors','specializations'));
    }

    public function contact()
    {
        $siteContent = \App\Models\SiteContent::first();
        return view('public.contact', compact('siteContent'));
    }

    public function contactStore(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:100',
            'email' => 'required|email|max:120',
            'phone' => 'nullable|string|max:20',
            'subject' => 'nullable|string|max:120',
            'message' => 'required|string|max:3000',
        ]);
        ContactMessage::create($data);
        Notify::admins('New contact message', $data['name'].': '.substr($data['message'], 0, 80), route('admin.contacts.index'), 'mail');
        
        try {
            Mail::to('lalje056@gmail.com')->send(new ContactMessageMail($data));
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Failed to send contact email: ' . $e->getMessage());
        }

        return back()->with('success', 'Thanks! We will get back to you within 24 hours.');
    }

    public function switchLang(string $lang)
    {
        if (in_array($lang, ['en','hi'])) {
            session(['locale' => $lang]);
        }
        return back();
    }
}
