<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<title>Rx {{ $prescription->prescription_code }}</title>
<style>
    body { font-family: DejaVu Sans, sans-serif; color:#1f2937; font-size:12px; }
    .h { width:100%; border-collapse:collapse; margin-bottom:18px; }
    .h td { border-bottom: 3px solid #0ea5e9; padding-bottom:10px; }
    .brand { font-size:22px; font-weight:bold; color:#0369a1; }
    .brand span { color:#0ea5e9; }
    .subtitle { color:#64748b; font-size:11px; }
    .grid2 { width:100%; margin-bottom:18px; }
    .grid2 td { vertical-align:top; width:50%; padding:8px 10px; background:#f8fafc; border-radius:6px; }
    h3 { font-size:12px; text-transform:uppercase; color:#0ea5e9; margin:15px 0 6px; letter-spacing:1px; }
    table.items { width:100%; border-collapse: collapse; margin-top:8px; }
    table.items th { background:#f1f5f9; text-align:left; padding:8px; font-size:11px; text-transform:uppercase; color:#475569; }
    table.items td { padding:8px; border-bottom:1px solid #e2e8f0; }
    .note { padding:10px 12px; background:#fef3c7; border-left:3px solid #f59e0b; margin-top:18px; border-radius:4px; }
    .footer { margin-top:40px; border-top:1px solid #e2e8f0; padding-top:10px; font-size:10px; color:#94a3b8; text-align:center; }
    .rx { display:inline-block; background:#0ea5e9; color:white; padding:3px 10px; border-radius:4px; font-weight:bold; }
</style>
</head>
<body>

@php
    // dompdf needs the GD extension to rasterise PNG/JPG; skip the logo gracefully if it's unavailable.
    $logoPath = public_path('assets/site_images/lklogo.png');
    $logoSrc = (extension_loaded('gd') && file_exists($logoPath))
        ? 'data:image/png;base64,'.base64_encode(file_get_contents($logoPath))
        : null;
@endphp

<table class="h">
    <tr>
        <td style="vertical-align:bottom; text-align:left;">
            <table style="border-collapse:collapse;"><tr>
                @if($logoSrc)
                    <td style="vertical-align:middle; padding-right:10px; border:0;"><img src="{{ $logoSrc }}" alt="LK Healthcare" style="height:46px;"></td>
                    <td style="vertical-align:middle; border:0;">
                        <div class="brand"><span>Healthcare</span></div>
                        <div class="subtitle">Qualified digital prescription</div>
                    </td>
                @else
                    <td style="vertical-align:middle; border:0;">
                        <div class="brand">LK <span>Healthcare</span></div>
                        <div class="subtitle">Qualified digital prescription</div>
                    </td>
                @endif
            </tr></table>
        </td>
        <td style="vertical-align:top; text-align:right;">
            <div class="rx">Rx</div>
            <div style="margin-top:4px; font-size:11px; color:#64748b;">{{ $prescription->prescription_code }}</div>
            <div style="font-size:11px; color:#64748b;">{{ $prescription->created_at->format('d M Y') }}</div>
        </td>
    </tr>
</table>

<table class="grid2">
    <tr>
        <td>
            <div style="font-size:10px; color:#64748b; text-transform:uppercase;">Doctor</div>
            <div style="font-weight:bold; font-size:14px; margin-top:2px;">Dr. {{ $prescription->doctor->user->name }}</div>
            <div style="color:#64748b;">{{ $prescription->doctor->specialization->name ?? '' }}</div>
            @if($prescription->doctor->qualification)<div style="color:#64748b; font-size:10px;">{{ $prescription->doctor->qualification }}</div>@endif
        </td>
        <td>
            <div style="font-size:10px; color:#64748b; text-transform:uppercase;">Patient</div>
            <div style="font-weight:bold; font-size:14px; margin-top:2px;">{{ $prescription->patient->user->name }}</div>
            <div style="color:#64748b;">ID: {{ $prescription->patient->patient_id ?? '-' }}</div>
            @if($prescription->patient->dob)<div style="color:#64748b; font-size:10px;">Age: {{ $prescription->patient->dob->age }} · {{ ucfirst($prescription->patient->gender ?? '') }}</div>@endif
        </td>
    </tr>
</table>

@if($prescription->diagnosis)
<h3>Diagnosis</h3>
<div>{{ $prescription->diagnosis }}</div>
@endif

<h3>Rx — Medicines</h3>
<table class="items">
    <thead>
        <tr>
            <th>#</th><th>Medicine</th><th>Dosage</th><th>Frequency</th><th>Duration</th><th>Instructions</th>
        </tr>
    </thead>
    <tbody>
        @foreach($prescription->items as $i => $it)
            <tr>
                <td>{{ $i+1 }}</td>
                <td style="font-weight:bold;">{{ $it->medicine_name }}</td>
                <td>{{ $it->dosage }}</td>
                <td>{{ $it->frequency }}</td>
                <td>{{ $it->duration }}</td>
                <td style="color:#64748b;">{{ $it->instructions }}</td>
            </tr>
        @endforeach
    </tbody>
</table>

@if($prescription->advice)
<h3>Advice</h3>
<div>{{ $prescription->advice }}</div>
@endif

@if($prescription->follow_up_date)
<div class="note"><strong>Follow-up:</strong> {{ $prescription->follow_up_date->format('d M Y') }}</div>
@endif

<div style="margin-top:50px; text-align:right;">
    <div style="display:inline-block; text-align:center;">
        <div style="border-top:1px solid #64748b; padding-top:4px; width:200px;">Dr. {{ $prescription->doctor->user->name }}</div>
        <div style="font-size:10px; color:#64748b;">Digital signature</div>
    </div>
</div>

<div class="footer">
    LK Healthcare · contact@lkhealthcare.in · +91 1800-LK-HEALTH<br>
    This is a computer-generated prescription. Verify at lkhealthcare.in/verify/{{ $prescription->prescription_code }}
</div>

@if(!empty($autoPrint))
<script>
    window.addEventListener('load', function () {
        setTimeout(function () { window.print(); }, 150);
    });
</script>
@endif

</body>
</html>
