<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Doctor;
use App\Models\Specialization;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class DoctorController extends Controller
{
    public function index(Request $request)
    {
        $q = trim((string) $request->q);
        $doctors = Doctor::with('user','specialization')
            ->when($q, fn($qr)=>$qr->whereHas('user', fn($u)=>$u->where('name','like',"%$q%")->orWhere('email','like',"%$q%")))
            ->latest()->paginate(15)->withQueryString();
        return view('admin.doctors.index', compact('doctors','q'));
    }

    public function create()
    {
        $specializations = Specialization::all();
        return view('admin.doctors.create', compact('specializations'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:150',
            'email' => 'required|email|unique:users,email',
            'phone' => 'nullable|string|max:20',
            'password' => 'required|string|min:6',
            'avatar' => 'nullable|image|mimes:jpeg,jpg,png,webp|max:2048',
            'specialization_id' => 'required|exists:specializations,id',
            'experience_years' => 'required|integer|min:0|max:60',
            'consultation_fee' => 'required|numeric|min:0',
            'qualification' => 'nullable|string|max:150',
        ]);

        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'phone' => $data['phone'] ?? null,
            'password' => Hash::make($data['password']),
        ]);
        $role = Role::firstOrCreate(['name' => 'doctor']);
        $user->assignRole($role);

        if ($request->hasFile('avatar')) {
            $user->avatar = $user->storeAvatar($request->file('avatar'));
            $user->save();
        }

        Doctor::create([
            'user_id' => $user->id,
            'specialization_id' => $data['specialization_id'],
            'experience_years' => $data['experience_years'],
            'consultation_fee' => $data['consultation_fee'],
            'qualification' => $data['qualification'] ?? null,
            'is_active' => true,
        ]);

        return redirect()->route('admin.doctors.index')->with('success', 'Doctor added.');
    }

    public function toggle(Doctor $doctor)
    {
        $doctor->update(['is_active' => !$doctor->is_active]);
        return back()->with('success', 'Status updated.');
    }

    public function toggleActive(Doctor $doctor)
    {
        $doctor->user->update(['is_active' => ! $doctor->user->is_active]);
        return back()->with('success', $doctor->user->is_active
            ? __('Doctor account activated.')
            : __('Doctor account deactivated. They will not be able to log in.'));
    }
}
