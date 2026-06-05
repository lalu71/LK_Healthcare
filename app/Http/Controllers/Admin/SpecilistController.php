<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Models\Specilist;

class SpecilistController extends Controller
{
    // List
    public function index(Request $request)
    {
        $q = trim((string) $request->q);
        $status = $request->status;

        $specilists = Specilist::query()
            ->when($q !== '', fn($qr) => $qr->where('name', 'like', "%{$q}%"))
            ->when($status !== null && $status !== '', fn($qr) => $qr->where('status', $status))
            ->latest()
            ->get();

        return view('admin.specilists.index', compact('specilists', 'q', 'status'));
    }

    // Create
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'   => 'required|string|max:255',
            'icon'   => 'nullable|image|mimes:jpg,jpeg,png,webp,svg|max:2048',
            'status' => 'required',
        ]);

        $iconName = null;

        if ($request->hasFile('icon')) {
            $iconName = time() . '_' . Str::random(8) . '.' . $request->file('icon')->getClientOriginalExtension();
            $request->file('icon')->move(public_path('assets/specilist'), $iconName);
        }

        Specilist::create([
            'name'   => $validated['name'],
            'icon'   => $iconName,
            'status' => $validated['status'],
        ]);

        return redirect()->back()->with('success', 'Specilist Added Successfully!');
    }

    // Update
    public function update(Request $request, $id)
    {
        $specilist = Specilist::findOrFail($id);

        $validated = $request->validate([
            'name'   => 'required|string|max:255',
            'icon'   => 'nullable|image|mimes:jpg,jpeg,png,webp,svg|max:2048',
            'status' => 'required',
        ]);

        $iconName = $specilist->icon;

        if ($request->hasFile('icon')) {
            if ($specilist->icon && file_exists(public_path('assets/specilist/' . $specilist->icon))) {
                @unlink(public_path('assets/specilist/' . $specilist->icon));
            }
            $iconName = time() . '_' . Str::random(8) . '.' . $request->file('icon')->getClientOriginalExtension();
            $request->file('icon')->move(public_path('assets/specilist'), $iconName);
        }

        $specilist->update([
            'name'   => $validated['name'],
            'icon'   => $iconName,
            'status' => $validated['status'],
        ]);

        return redirect()->back()->with('success', 'Specilist Updated Successfully!');
    }

    // Delete
    public function destroy($id)
    {
        $specilist = Specilist::findOrFail($id);

        if ($specilist->icon && file_exists(public_path('assets/specilist/' . $specilist->icon))) {
            @unlink(public_path('assets/specilist/' . $specilist->icon));
        }

        $specilist->delete();

        return redirect()->back()->with('success', 'Specilist Deleted Successfully!');
    }
}
