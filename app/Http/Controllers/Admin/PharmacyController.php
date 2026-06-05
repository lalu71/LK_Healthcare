<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Medicine;
use App\Models\PharmacyOrder;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Spatie\Permission\Models\Role;

class PharmacyController extends Controller
{
    public function index(Request $request)
    {
        $q = trim((string) $request->q);
        $status = $request->status; // 'critical' | 'low' | 'healthy' | ''

        $medicines = Medicine::query()
            ->when($q !== '', fn($qr) => $qr->where(function($w) use ($q) {
                $w->where('name', 'like', "%{$q}%")
                  ->orWhere('manufacturer', 'like', "%{$q}%")
                  ->orWhere('category', 'like', "%{$q}%");
            }))
            ->when($status === 'critical', fn($qr) => $qr->where('stock', '<', 10))
            ->when($status === 'low', fn($qr) => $qr->where('stock', '>=', 10)->where('stock', '<', 50))
            ->when($status === 'healthy', fn($qr) => $qr->where('stock', '>=', 50))
            ->orderBy('name')
            ->paginate(5)
            ->withQueryString();

        $orders = PharmacyOrder::with('patient.user')->latest()->limit(10)->get();
        $pharmacists = User::role('pharmacist')->latest()->get();
        return view('admin.pharmacy.index', compact('medicines','orders','pharmacists','q','status'));
    }

    public function storePharmacist(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:150',
            'email' => 'required|email|unique:users,email',
            'phone' => 'nullable|string|max:20',
            'password' => 'required|string|min:6',
            'avatar' => 'nullable|image|mimes:jpeg,jpg,png,webp|max:2048',
        ]);

        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'phone' => $data['phone'] ?? null,
            'password' => Hash::make($data['password']),
            'is_active' => true,
        ]);
        $role = Role::firstOrCreate(['name' => 'pharmacist']);
        $user->assignRole($role);

        if ($request->hasFile('avatar')) {
            $user->avatar = $user->storeAvatar($request->file('avatar'));
            $user->save();
        }

        return back()->with('success', __('Pharmacist account created successfully.'));
    }

    public function togglePharmacist(User $user)
    {
        abort_unless($user->hasRole('pharmacist'), 404);
        $user->update(['is_active' => ! $user->is_active]);
        return back()->with('success', $user->is_active
            ? __('Pharmacist account activated.')
            : __('Pharmacist account deactivated. They cannot log in.'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:150',
            'category' => 'nullable|string|max:80',
            'manufacturer' => 'nullable|string|max:120',
            'price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'unit' => 'required|string|in:strip,bottle,tube,tablet,box',
            'requires_prescription' => 'nullable|boolean',
        ]);
        Medicine::create($data + ['is_active' => true]);
        return back()->with('success','Medicine added.');
    }

    public function update(Request $request, Medicine $medicine)
    {
        $data = $request->validate([
            'price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
        ]);
        $medicine->update($data);
        return back()->with('success','Updated.');
    }

    public function destroy(Medicine $medicine)
    {
        $medicine->update(['is_active' => false]);
        return back()->with('success','Medicine deactivated.');
    }

    public function updateOrder(Request $request, PharmacyOrder $order)
    {
        $request->validate(['status' => 'required|in:placed,packed,shipped,delivered,cancelled']);
        $order->update(['status' => $request->status]);
        return back()->with('success','Order updated.');
    }
}
