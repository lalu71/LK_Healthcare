@extends('layouts.app')
@section('title', __('Admin Dashboard'))
@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-6">

    {{-- Page heading + Date Filter --}}
    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
        <div>
            <h1 class="text-3xl font-extrabold text-slate-900">{{ __('Dashboard') }}</h1>
            <p class="text-slate-500 text-sm mt-1">{{ __('System health, operations, and revenue at a glance.') }}</p>
        </div>

        {{-- Clean date range filter --}}
        <form method="GET" action="{{ route('dashboard') }}" class="flex items-center gap-2 flex-wrap">
            <input type="date" name="from" value="{{ $filter['from'] }}" max="{{ now()->toDateString() }}"
                class="h-10 px-3 rounded-lg border-slate-200 bg-white focus:border-teal-500 focus:ring-teal-500 text-sm font-medium text-slate-700">
            <span class="text-slate-400 text-sm">—</span>
            <input type="date" name="to" value="{{ $filter['to'] }}" max="{{ now()->toDateString() }}"
                class="h-10 px-3 rounded-lg border-slate-200 bg-white focus:border-teal-500 focus:ring-teal-500 text-sm font-medium text-slate-700">
            <button class="h-10 px-4 rounded-lg bg-teal-600 text-white font-bold text-sm hover:bg-teal-700 transition">{{ __('Apply') }}</button>
            @if($filter['active'])
                <a href="{{ route('dashboard') }}"
                   class="h-10 px-3 inline-flex items-center gap-1.5 rounded-lg bg-white border border-slate-200 text-slate-600 font-bold text-sm hover:bg-rose-50 hover:border-rose-200 hover:text-rose-600 transition">
                    <x-icon name="x" class="h-3.5 w-3.5"/> {{ __('Reset') }}
                </a>
            @endif
        </form>
    </div>

    @if($errors->any())
        <div class="text-rose-600 text-xs font-bold">{{ $errors->first() }}</div>
    @endif

    {{-- Key Stats — pastel style --}}
    <div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-4">
        @php
            $keyStats = [
                ['label'=>__('Total Patients'),  'value'=>$stats['patients'],                       'icon'=>'users',       'bg'=>'bg-rose-100',     'border'=>'border-rose-200',     'iconBg'=>'bg-rose-500',     'sub'=>'+5% '.__('vs last month'),  'subColor'=>'text-rose-700'],
                ['label'=>__('Doctors'),         'value'=>$stats['doctors'],                        'icon'=>'stethoscope', 'bg'=>'bg-emerald-100',  'border'=>'border-emerald-200',  'iconBg'=>'bg-emerald-500',  'sub'=>'+2 '.__('this month'),      'subColor'=>'text-emerald-700'],
                ['label'=>__("Today's Appts"),   'value'=>$stats['appointments_today'],             'icon'=>'calendar',    'bg'=>'bg-sky-100',      'border'=>'border-sky-200',      'iconBg'=>'bg-sky-500',      'sub'=>__('Live now'),               'subColor'=>'text-sky-700'],
                ['label'=>__('Revenue'),         'value'=>'₹'.number_format($stats['revenue'],0),   'icon'=>'credit-card', 'bg'=>'bg-amber-100',    'border'=>'border-amber-200',    'iconBg'=>'bg-amber-500',    'sub'=>'+12% '.__('vs last month'), 'subColor'=>'text-amber-700'],
            ];
        @endphp
        @foreach($keyStats as $s)
            <div class="{{ $s['bg'] }} {{ $s['border'] }} border rounded-2xl p-5 shadow-sm transition hover:shadow-lg hover:-translate-y-0.5">
                <div class="flex items-start gap-4">
                    <div class="h-12 w-12 rounded-xl {{ $s['iconBg'] }} text-white flex items-center justify-center shadow-md shrink-0">
                        <x-icon :name="$s['icon']" class="h-6 w-6"/>
                    </div>
                    <div class="min-w-0 flex-1">
                        <div class="text-xs font-bold text-slate-700">{{ $s['label'] }}</div>
                        <div class="text-3xl font-extrabold text-slate-900 mt-1 leading-tight">{{ $s['value'] }}</div>
                        <div class="text-[11px] font-bold mt-2 {{ $s['subColor'] }}">{{ $s['sub'] }}</div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    {{-- Activity metric cards --}}
    @php
        $metricCards = [
            ['value'=>$stats['appointments_total'],  'label'=>__('Total Appointments'),  'icon'=>'calendar', 'iconBg'=>'bg-orange-100', 'iconColor'=>'text-orange-600', 'delta'=>'+35%', 'deltaColor'=>'text-emerald-600', 'deltaText'=>__('vs Last Month'), 'href'=>route('admin.appointments.index')],
            ['value'=>$stats['lab_bookings'],        'label'=>__('Lab Bookings'),        'icon'=>'flask',    'iconBg'=>'bg-rose-100',   'iconColor'=>'text-rose-600',   'delta'=>'+12%', 'deltaColor'=>'text-emerald-600', 'deltaText'=>__('vs Last Month'), 'href'=>'#'],
            ['value'=>$stats['pharmacy_orders'],     'label'=>__('Pharmacy Orders'),     'icon'=>'cart',     'iconBg'=>'bg-amber-100',  'iconColor'=>'text-amber-600',  'delta'=>'+8%',  'deltaColor'=>'text-emerald-600', 'deltaText'=>__('vs Last Month'), 'href'=>'#'],
        ];
    @endphp
    <div class="grid md:grid-cols-3 gap-4">
        @foreach($metricCards as $m)
            <div class="bg-white rounded-2xl border border-slate-200 p-5 transition hover:shadow-md">
                <div class="flex items-start justify-between gap-3">
                    <div>
                        <div class="text-3xl font-extrabold text-slate-900 leading-none">{{ $m['value'] }}</div>
                        <div class="text-sm font-semibold text-slate-600 mt-1.5">{{ $m['label'] }}</div>
                    </div>
                    <div class="h-12 w-12 rounded-xl {{ $m['iconBg'] }} {{ $m['iconColor'] }} flex items-center justify-center shrink-0">
                        <x-icon :name="$m['icon']" class="h-6 w-6"/>
                    </div>
                </div>
                <div class="my-4 border-t border-slate-100"></div>
                <div class="flex items-center justify-between text-xs">
                    <div>
                        <span class="font-extrabold {{ $m['deltaColor'] }}">{{ $m['delta'] }}</span>
                        <span class="text-slate-500 font-semibold">{{ $m['deltaText'] }}</span>
                    </div>
                    <a href="{{ $m['href'] }}" class="font-bold text-teal-600 hover:underline">{{ __('View') }}</a>
                </div>
            </div>
        @endforeach
    </div>

    {{-- Revenue Trend (30 days area chart) --}}
    <div class="bg-white rounded-2xl border border-slate-200 p-5">
        <div class="flex items-center justify-between mb-5 flex-wrap gap-2">
            <div>
                <h3 class="text-lg font-bold text-slate-800">{{ __('Revenue Trend') }}</h3>
                <div class="text-xs text-slate-500">{{ __('Daily collections — last 30 days') }}</div>
            </div>
            <div class="flex items-center gap-4 text-xs">
                <div class="flex items-center gap-2">
                    <span class="h-2.5 w-2.5 rounded-full bg-teal-500"></span>
                    <span class="font-semibold text-slate-600">{{ __('Revenue') }}</span>
                </div>
                @php $trendTotal = array_sum($revenueTrend['data']); @endphp
                <div class="font-extrabold text-slate-900">₹{{ number_format($trendTotal, 0) }} <span class="text-slate-400 font-semibold text-[10px] uppercase tracking-widest ml-1">{{ __('total') }}</span></div>
            </div>
        </div>
        <div class="h-64">
            <canvas id="revenueTrendChart"></canvas>
        </div>
    </div>

    {{-- Revenue Breakdown: Donut + Period tiles --}}
    <div class="grid lg:grid-cols-3 gap-6">
        {{-- By Source — Donut --}}
        <div class="bg-white rounded-2xl border border-slate-200 p-5">
            <div class="flex items-center justify-between mb-5">
                <h3 class="text-lg font-bold text-slate-800">{{ __('Revenue by Source') }}</h3>
                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">
                    {{ $filter['active'] ? __('Filtered') : __('All time') }}
                </span>
            </div>
            <div class="relative h-56 flex items-center justify-center">
                <canvas id="revenueSourceChart"></canvas>
                @php $totalSource = array_sum($revenueBySource); @endphp
                <div class="absolute inset-0 flex flex-col items-center justify-center pointer-events-none">
                    <div class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">{{ __('Total') }}</div>
                    <div class="text-xl font-extrabold text-slate-900">₹{{ number_format($totalSource, 0) }}</div>
                </div>
            </div>
            @php
                $sources = [
                    ['key'=>'appointments', 'label'=>__('Appointments'), 'color'=>'#14b8a6'],
                    ['key'=>'lab',          'label'=>__('Lab Tests'),    'color'=>'#8b5cf6'],
                    ['key'=>'pharmacy',     'label'=>__('Medicines'),    'color'=>'#10b981'],
                ];
            @endphp
            <div class="mt-4 space-y-2 text-xs">
                @foreach($sources as $s)
                    @php
                        $v = $revenueBySource[$s['key']] ?? 0;
                        $pct = $totalSource > 0 ? round(($v / $totalSource) * 100) : 0;
                    @endphp
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <span class="h-2.5 w-2.5 rounded-full" style="background:{{ $s['color'] }}"></span>
                            <span class="font-semibold text-slate-600">{{ $s['label'] }}</span>
                        </div>
                        <div class="font-bold text-slate-900">₹{{ number_format($v, 0) }} <span class="text-slate-400 font-semibold">· {{ $pct }}%</span></div>
                    </div>
                @endforeach
            </div>
        </div>

        {{-- By Period --}}
        <div class="lg:col-span-2 bg-white rounded-2xl border border-slate-200 p-5">
            <div class="flex items-center justify-between mb-5">
                <h3 class="text-lg font-bold text-slate-800">{{ __('Revenue by Period') }}</h3>
                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">{{ now()->translatedFormat('M Y') }}</span>
            </div>
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                @foreach([
                    ['key'=>'today', 'label'=>__('Today'),      'icon'=>'calendar',    'color'=>'amber'],
                    ['key'=>'week',  'label'=>__('This Week'),  'icon'=>'calendar',    'color'=>'teal'],
                    ['key'=>'month', 'label'=>__('This Month'), 'icon'=>'credit-card', 'color'=>'emerald'],
                    ['key'=>'year',  'label'=>__('This Year'),  'icon'=>'credit-card', 'color'=>'violet'],
                ] as $p)
                    <div class="rounded-xl border border-slate-100 bg-gradient-to-br from-slate-50 to-white p-4 hover:shadow-md transition">
                        <div class="flex items-center gap-2 text-slate-500 mb-1.5">
                            <x-icon :name="$p['icon']" class="h-4 w-4 text-{{ $p['color'] }}-500"/>
                            <span class="text-[10px] font-bold uppercase tracking-widest">{{ $p['label'] }}</span>
                        </div>
                        <div class="text-2xl font-extrabold text-slate-900">₹{{ number_format($revenueByPeriod[$p['key']] ?? 0, 0) }}</div>
                    </div>
                @endforeach
            </div>

            {{-- Appointment status breakdown bars --}}
            @if(!empty($statusBreakdown))
                <div class="mt-6 pt-6 border-t border-slate-100">
                    <div class="flex items-center justify-between mb-4">
                        <h4 class="font-bold text-slate-800 text-sm">{{ __('Appointment Status') }}</h4>
                        <span class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">{{ __('All time') }}</span>
                    </div>
                    @php
                        $statusColors = ['pending' => ['bg-amber-500', 'bg-amber-100', 'text-amber-700'],
                                         'confirmed' => ['bg-emerald-500', 'bg-emerald-100', 'text-emerald-700'],
                                         'completed' => ['bg-teal-500', 'bg-teal-100', 'text-teal-700'],
                                         'cancelled' => ['bg-rose-500', 'bg-rose-100', 'text-rose-700']];
                        $statusTotal = max(1, array_sum($statusBreakdown));
                    @endphp
                    <div class="space-y-2.5">
                        @foreach($statusBreakdown as $st => $count)
                            @php
                                $colors = $statusColors[$st] ?? ['bg-slate-500', 'bg-slate-100', 'text-slate-700'];
                                $pct = round(($count / $statusTotal) * 100);
                            @endphp
                            <div>
                                <div class="flex items-center justify-between text-xs mb-1">
                                    <span class="font-semibold text-slate-600 capitalize">{{ __($st) }}</span>
                                    <span class="font-bold text-slate-900">{{ $count }} <span class="text-slate-400 font-semibold">· {{ $pct }}%</span></span>
                                </div>
                                <div class="h-1.5 {{ $colors[1] }} rounded-full overflow-hidden">
                                    <div class="h-full {{ $colors[0] }} rounded-full" style="width: {{ $pct }}%"></div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
    </div>

    <div class="grid lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 bg-white rounded-2xl border border-slate-200 overflow-hidden">
            <div class="p-5 border-b border-slate-200 flex items-center justify-between">
                <h3 class="text-lg font-bold text-slate-800">{{ __('Recent Appointments') }}</h3>
                <a href="{{ route('admin.appointments.index') }}" class="text-sm text-teal-600 font-semibold hover:underline">{{ __('View all') }} →</a>
            </div>
            <div class="divide-y divide-slate-50">
                @forelse($recentAppointments as $apt)
                    <div class="p-4 flex items-center gap-4 hover:bg-slate-50">
                        <div class="h-10 w-10 rounded-xl bg-gradient-to-br from-teal-500 to-emerald-500 text-white flex items-center justify-center font-bold">{{ strtoupper(substr($apt->patient->user->name,0,1)) }}</div>
                        <div class="flex-1 min-w-0">
                            <div class="font-semibold text-slate-900 truncate">{{ $apt->patient->user->name }} → Dr. {{ $apt->doctor->user->name }}</div>
                            <div class="text-xs text-slate-500">{{ $apt->appointment_date->translatedFormat('d M, h:i A') }}</div>
                        </div>
                        <span class="px-2.5 py-1 rounded-full text-[10px] font-bold uppercase
                            {{ $apt->status === 'pending' ? 'bg-amber-100 text-amber-700' : '' }}
                            {{ $apt->status === 'confirmed' ? 'bg-emerald-100 text-emerald-700' : '' }}
                            {{ $apt->status === 'completed' ? 'bg-teal-100 text-teal-700' : '' }}
                            {{ $apt->status === 'cancelled' ? 'bg-rose-100 text-rose-700' : '' }}">
                            {{ __($apt->status) }}
                        </span>
                    </div>
                @empty
                    <div class="p-8 text-center text-slate-400">{{ __('No appointments yet.') }}</div>
                @endforelse
            </div>
        </div>

        <div class="bg-white rounded-2xl border border-slate-200 p-5">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <h3 class="font-bold text-slate-800">{{ __('Appointments') }}</h3>
                    <div class="text-[10px] font-semibold text-slate-400">{{ __('Last 14 days') }}</div>
                </div>
                <div class="text-right">
                    <div class="text-2xl font-extrabold text-slate-900">{{ array_sum($appointmentChart['data']) }}</div>
                    <div class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">{{ __('Total') }}</div>
                </div>
            </div>
            <div class="h-44">
                <canvas id="appointmentBarChart"></canvas>
            </div>
        </div>
    </div>

    {{-- Chart.js (CDN) + chart bootstrap --}}
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js" defer></script>
    <script>
        window.addEventListener('DOMContentLoaded', () => {
            if (typeof Chart === 'undefined') return;

            Chart.defaults.font.family = "'Inter', system-ui, sans-serif";
            Chart.defaults.color = '#64748b';
            Chart.defaults.borderColor = '#f1f5f9';

            // ── 1. Revenue Trend (area) ──────────────────────────────
            const rtEl = document.getElementById('revenueTrendChart');
            if (rtEl) {
                const ctx = rtEl.getContext('2d');
                const grad = ctx.createLinearGradient(0, 0, 0, 260);
                grad.addColorStop(0, 'rgba(20, 184, 166, 0.35)');
                grad.addColorStop(1, 'rgba(20, 184, 166, 0)');
                new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels: @json($revenueTrend['labels']),
                        datasets: [{
                            label: 'Revenue',
                            data: @json($revenueTrend['data']),
                            fill: true,
                            backgroundColor: grad,
                            borderColor: '#14b8a6',
                            borderWidth: 2.5,
                            tension: 0.35,
                            pointRadius: 0,
                            pointHoverRadius: 5,
                            pointHoverBackgroundColor: '#14b8a6',
                            pointHoverBorderColor: '#fff',
                            pointHoverBorderWidth: 2,
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: { display: false },
                            tooltip: {
                                backgroundColor: '#0f172a',
                                titleColor: '#fff',
                                bodyColor: '#cbd5e1',
                                padding: 10,
                                cornerRadius: 10,
                                displayColors: false,
                                callbacks: {
                                    label: (ctx) => '  ₹' + Number(ctx.parsed.y).toLocaleString('en-IN'),
                                }
                            }
                        },
                        scales: {
                            x: { grid: { display: false }, ticks: { maxRotation: 0, autoSkip: true, maxTicksLimit: 8, font: { size: 10 } } },
                            y: { grid: { color: '#f1f5f9' }, beginAtZero: true, ticks: { callback: v => '₹' + (v >= 1000 ? (v/1000).toFixed(0) + 'k' : v), font: { size: 10 } } }
                        }
                    }
                });
            }

            // ── 2. Revenue by Source (donut) ──────────────────────────
            const rsEl = document.getElementById('revenueSourceChart');
            if (rsEl) {
                new Chart(rsEl.getContext('2d'), {
                    type: 'doughnut',
                    data: {
                        labels: ['Appointments', 'Lab Tests', 'Medicines'],
                        datasets: [{
                            data: [
                                {{ $revenueBySource['appointments'] ?? 0 }},
                                {{ $revenueBySource['lab'] ?? 0 }},
                                {{ $revenueBySource['pharmacy'] ?? 0 }},
                            ],
                            backgroundColor: ['#14b8a6', '#8b5cf6', '#10b981'],
                            borderColor: '#fff',
                            borderWidth: 3,
                            hoverOffset: 8,
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        cutout: '72%',
                        plugins: {
                            legend: { display: false },
                            tooltip: {
                                backgroundColor: '#0f172a',
                                titleColor: '#fff',
                                bodyColor: '#cbd5e1',
                                padding: 10,
                                cornerRadius: 10,
                                callbacks: {
                                    label: (ctx) => '  ' + ctx.label + ': ₹' + Number(ctx.parsed).toLocaleString('en-IN'),
                                }
                            }
                        }
                    }
                });
            }

            // ── 3. Appointments (bar) ─────────────────────────────────
            const apEl = document.getElementById('appointmentBarChart');
            if (apEl) {
                const ctx = apEl.getContext('2d');
                const grad = ctx.createLinearGradient(0, 0, 0, 180);
                grad.addColorStop(0, '#14b8a6');
                grad.addColorStop(1, '#0d9488');
                new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: @json($appointmentChart['labels']),
                        datasets: [{
                            label: 'Appointments',
                            data: @json($appointmentChart['data']),
                            backgroundColor: grad,
                            borderRadius: 6,
                            borderSkipped: false,
                            maxBarThickness: 14,
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: { display: false },
                            tooltip: {
                                backgroundColor: '#0f172a',
                                titleColor: '#fff',
                                bodyColor: '#cbd5e1',
                                padding: 10,
                                cornerRadius: 10,
                                displayColors: false,
                            }
                        },
                        scales: {
                            x: { grid: { display: false }, ticks: { maxRotation: 0, autoSkip: true, maxTicksLimit: 7, font: { size: 9 } } },
                            y: { grid: { color: '#f1f5f9' }, beginAtZero: true, ticks: { precision: 0, font: { size: 10 } } }
                        }
                    }
                });
            }
        });
    </script>

    <div class="grid sm:grid-cols-2 md:grid-cols-4 gap-4">
        @foreach([
            ['r'=>route('admin.doctors.create'),'t'=>__('Add Doctor'),'i'=>'plus','c'=>'emerald'],
            ['r'=>route('admin.pharmacy.index'),'t'=>__('Pharmacy Inventory'),'i'=>'pill','c'=>'violet'],
            ['r'=>route('admin.blood.index'),'t'=>__('Blood Bank'),'i'=>'droplet','c'=>'rose'],
            ['r'=>route('admin.emergency.index'),'t'=>__('Emergency'),'i'=>'ambulance','c'=>'amber'],
        ] as $q)
            <a href="{{ $q['r'] }}" class="bg-white rounded-2xl border border-slate-200 p-5 hover:shadow-md hover:-translate-y-0.5 transition text-center">
                <div class="h-10 w-10 mx-auto rounded-xl bg-{{ $q['c'] }}-50 text-{{ $q['c'] }}-600 flex items-center justify-center"><x-icon :name="$q['i']" class="h-5 w-5"/></div>
                <div class="mt-2 font-semibold text-sm text-slate-800">{{ $q['t'] }}</div>
            </a>
        @endforeach
    </div>
</div>
@endsection
