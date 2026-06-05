<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Models\Service;
use App\Models\SiteContent;

class AdminPublicController extends Controller
{
    // Admin Services List
    public function index(Request $request)
    {
        $q = trim((string) $request->q);
        $status = $request->status;

        $services = Service::query()
            ->when($q !== '', fn($qr) => $qr->where(function($w) use ($q) {
                $w->where('title', 'like', "%{$q}%")
                  ->orWhere('short_discription', 'like', "%{$q}%");
            }))
            ->when($status !== null && $status !== '', fn($qr) => $qr->where('status', $status))
            ->latest()
            ->get();

        return view('admin.services.index', compact('services', 'q', 'status'));
    }

    // Admin Add Service
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title'             => 'required|string|max:255',
            'short_discription' => 'required|string',
            'image'             => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'status'            => 'required',
        ]);

        $imageName = null;

        if ($request->hasFile('image')) {
            $imageName = time() . '_' . Str::random(8) . '.' . $request->file('image')->getClientOriginalExtension();
            $request->file('image')->move(public_path('assets/service'), $imageName);
        }

        Service::create([
            'title'             => $validated['title'],
            'short_discription' => $validated['short_discription'],
            'image'             => $imageName,
            'status'            => $validated['status'],
        ]);

        return redirect()->back()->with('success', 'Service Added Successfully!');
    }

    // Admin Update Service
    public function update(Request $request, $id)
    {
        $service = Service::findOrFail($id);

        $validated = $request->validate([
            'title'             => 'required|string|max:255',
            'short_discription' => 'required|string',
            'image'             => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'status'            => 'required',
        ]);

        $imageName = $service->image;

        if ($request->hasFile('image')) {
            // Remove old image if present
            if ($service->image && file_exists(public_path('assets/service/' . $service->image))) {
                @unlink(public_path('assets/service/' . $service->image));
            }
            $imageName = time() . '_' . Str::random(8) . '.' . $request->file('image')->getClientOriginalExtension();
            $request->file('image')->move(public_path('assets/service'), $imageName);
        }

        $service->update([
            'title'             => $validated['title'],
            'short_discription' => $validated['short_discription'],
            'image'             => $imageName,
            'status'            => $validated['status'],
        ]);

        return redirect()->back()->with('success', 'Service Updated Successfully!');
    }

    // Admin Service Delete
    public function destroy($id)
    {
        $service = Service::findOrFail($id);

        if ($service->image && file_exists(public_path('assets/service/' . $service->image))) {
            @unlink(public_path('assets/service/' . $service->image));
        }

        $service->delete();

        return redirect()->back()->with('success', 'Service Deleted Successfully!');
    }

    // Site Content Update Code
    public function site_content_update(Request $request){
        $sitecontent = SiteContent::findorFail(1);

        $validated = $request->validate([
            'site_name'             => 'required|string|max:255',
            'site_title'            => 'required|string',
            'site_description'      => 'required|string',
            'help_contact'          => 'required|numeric|digits:10',
            'follow_by'             => 'required|string',
            'site_email'            => 'required|email|max:255',
            'site_address'          => 'required|string',
        ]);

        $sitecontent->update([
            'site_name'             => $validated['site_name'],
            'site_title'            => $validated['site_title'],
            'site_description'      => $validated['site_description'],
            'help_contact'          => $validated['help_contact'],
            'follow_by'             => $validated['follow_by'],
            'site_email'            => $validated['site_email'],
            'site_address'          => $validated['site_address'],
        ]);

        $sitecontent->update($validated);
        return redirect()->back()->with('success', 'Site Content Updated Successfully!');
    }

    // Site content view code
    public function site_content(){
        $sitecontent = SiteContent::first();
        return view('admin.sitecontent', compact('sitecontent'));
    }
}
