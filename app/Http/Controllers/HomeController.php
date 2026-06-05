<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\Doctor;
use App\Models\MedicalRecord;
use App\Models\Patient;
use App\Models\Prescription;
use App\Models\User;
use App\Models\LabBooking;
use App\Models\PharmacyOrder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class HomeController extends Controller
{
    public function dashboard(Request $request)
    {
        $user = $request->user();

        if ($user->hasRole('admin')) {
            $request->validate([
                'from' => 'nullable|date',
                'to' => 'nullable|date|after_or_equal:from',
            ]);

            $from = $request->filled('from') ? \Carbon\Carbon::parse($request->input('from'))->startOfDay() : null;
            $to   = $request->filled('to')   ? \Carbon\Carbon::parse($request->input('to'))->endOfDay()   : null;

            $paid = function () use ($from, $to) {
                $q = DB::table('payments')->where('status', 'success');
                if ($from) $q->where('created_at', '>=', $from);
                if ($to)   $q->where('created_at', '<=', $to);
                return $q;
            };

            $applyRange = function ($query, string $column) use ($from, $to) {
                if ($from) $query->where($column, '>=', $from);
                if ($to)   $query->where($column, '<=', $to);
                return $query;
            };

            $stats = [
                'patients' => Patient::count(),
                'doctors' => Doctor::count(),
                'appointments_today' => Appointment::whereDate('appointment_date', today())->count(),
                'appointments_total' => $applyRange(Appointment::query(), 'appointment_date')->count(),
                'revenue' => $paid()->sum('amount'),
                'lab_bookings' => $applyRange(LabBooking::query(), 'created_at')->count(),
                'pharmacy_orders' => $applyRange(PharmacyOrder::query(), 'created_at')->count(),
            ];

            $revenueBySource = [
                'appointments' => (float) $paid()->where('payable_type', \App\Models\Appointment::class)->sum('amount'),
                'lab' => (float) $paid()->where('payable_type', \App\Models\LabBooking::class)->sum('amount'),
                'pharmacy' => (float) $paid()->whereIn('payable_type', [
                    \App\Models\PharmacyOrder::class,
                    \App\Models\Prescription::class,
                ])->sum('amount'),
            ];

            // Period tiles always show calendar-based ranges (independent of filter)
            $allPaid = fn () => DB::table('payments')->where('status', 'success');
            $revenueByPeriod = [
                'today' => (float) $allPaid()->whereDate('created_at', today())->sum('amount'),
                'week' => (float) $allPaid()->where('created_at', '>=', now()->startOfWeek())->sum('amount'),
                'month' => (float) $allPaid()->where('created_at', '>=', now()->startOfMonth())->sum('amount'),
                'year' => (float) $allPaid()->where('created_at', '>=', now()->startOfYear())->sum('amount'),
            ];

            $filter = [
                'from' => $request->input('from'),
                'to' => $request->input('to'),
                'active' => (bool) ($from || $to),
            ];

            $recentAppointments = Appointment::with('patient.user','doctor.user')->latest()->limit(6)->get();
            $appointmentTrend = Appointment::selectRaw('DATE(appointment_date) as d, COUNT(*) as c')
                ->where('appointment_date','>=', now()->subDays(7))
                ->groupBy('d')->orderBy('d')->get();

            // ── Chart datasets ──────────────────────────────────────────
            // 1. Revenue trend (last 30 days) — daily totals
            $revRaw = DB::table('payments')
                ->selectRaw('DATE(created_at) as d, SUM(amount) as total')
                ->where('status', 'success')
                ->where('created_at', '>=', now()->subDays(30))
                ->groupBy('d')->orderBy('d')->get()->keyBy('d');

            $revenueTrend = ['labels' => [], 'data' => []];
            for ($i = 29; $i >= 0; $i--) {
                $date = now()->subDays($i);
                $revenueTrend['labels'][] = $date->translatedFormat('d M');
                $revenueTrend['data'][] = (float) ($revRaw->get($date->toDateString())?->total ?? 0);
            }

            // 2. Appointments trend (last 14 days) — daily counts
            $apptRaw = Appointment::selectRaw('DATE(appointment_date) as d, COUNT(*) as c')
                ->where('appointment_date', '>=', now()->subDays(14))
                ->groupBy('d')->orderBy('d')->get()->keyBy('d');

            $appointmentChart = ['labels' => [], 'data' => []];
            for ($i = 13; $i >= 0; $i--) {
                $date = now()->subDays($i);
                $appointmentChart['labels'][] = $date->translatedFormat('d M');
                $appointmentChart['data'][] = (int) ($apptRaw->get($date->toDateString())?->c ?? 0);
            }

            // 3. Appointment status breakdown (for donut)
            $statusBreakdown = Appointment::selectRaw('status, COUNT(*) as c')
                ->groupBy('status')->pluck('c', 'status')->toArray();

            return view('admin.dashboard', compact(
                'stats', 'recentAppointments', 'appointmentTrend',
                'revenueBySource', 'revenueByPeriod', 'filter',
                'revenueTrend', 'appointmentChart', 'statusBreakdown'
            ));
        }

        if ($user->hasRole('doctor')) {
            $doctor = $user->doctor;
            $stats = ['today'=>0,'upcoming'=>0,'total'=>0,'patients'=>0];
            $upcoming = collect();
            if ($doctor) {
                $stats = [
                    'today' => Appointment::where('doctor_id',$doctor->id)->whereDate('appointment_date', today())->count(),
                    'upcoming' => Appointment::where('doctor_id',$doctor->id)->where('appointment_date','>', now())->whereNotIn('status',['cancelled','completed'])->count(),
                    'total' => Appointment::where('doctor_id',$doctor->id)->count(),
                    'patients' => Appointment::where('doctor_id',$doctor->id)->distinct('patient_id')->count('patient_id'),
                ];
                $upcoming = Appointment::with('patient.user')
                    ->where('doctor_id', $doctor->id)
                    ->where('appointment_date','>=', today())
                    ->whereNotIn('status',['cancelled','completed'])
                    ->orderBy('appointment_date','asc')->limit(8)->get();
            }
            return view('doctor.dashboard', compact('stats','upcoming','doctor'));
        }

        if ($user->hasRole('pharmacist')) {
            return redirect()->route('pharmacist.dashboard');
        }

        // Patient
        $patient = $user->patient;
        $stats = ['upcoming'=>0,'completed'=>0,'prescriptions'=>0,'records'=>0];
        $upcoming = collect();
        $recentPrescriptions = collect();
        if ($patient) {
            $stats = [
                'upcoming' => Appointment::where('patient_id',$patient->id)->where('appointment_date','>=', today())->whereIn('status',['pending','confirmed'])->count(),
                'completed' => Appointment::where('patient_id',$patient->id)->where('status','completed')->count(),
                'prescriptions' => Prescription::where('patient_id',$patient->id)->count(),
                'records' => MedicalRecord::where('patient_id',$patient->id)->count(),
            ];
            $upcoming = Appointment::with('doctor.user','doctor.specialization')
                ->where('patient_id',$patient->id)
                ->where('appointment_date','>=', today())
                ->whereIn('status',['pending','confirmed'])
                ->orderBy('appointment_date','asc')->limit(5)->get();
            $recentPrescriptions = Prescription::with('doctor.user')->where('patient_id',$patient->id)->latest()->limit(3)->get();
        }
        return view('patient.dashboard', compact('stats','upcoming','recentPrescriptions','patient'));
    }
}
