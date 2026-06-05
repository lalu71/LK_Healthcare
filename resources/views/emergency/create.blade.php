@extends(auth()->check() ? 'layouts.app' : 'layouts.public')
@section('title', __('Emergency'))
@section('content')
<div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    {{-- Guests use the public layout which has no global flash, so show messages here. --}}
    @guest
        @if(session('success'))
            <div class="mb-6 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 px-4 py-3 text-sm font-medium flex items-center gap-2">
                <x-icon name="check" class="h-4 w-4"/> {{ session('success') }}
            </div>
        @endif
    @endguest
    @if($errors->any())
        <div class="mb-6 rounded-xl bg-rose-50 border border-rose-200 text-rose-800 px-4 py-3 text-sm font-medium flex items-center gap-2">
            <x-icon name="alert" class="h-4 w-4"/> {{ $errors->first() }}
        </div>
    @endif
    <div class="rounded-2xl bg-gradient-to-r from-teal-600 via-teal-700 to-emerald-700 p-6 sm:p-8 text-white mb-6 relative overflow-hidden">
        <div class="absolute inset-0 opacity-15" style="background-image:radial-gradient(circle at 20% 30%, white 1px, transparent 1px); background-size:24px 24px;"></div>
        <div class="relative flex items-center gap-4">
            <div class="h-14 w-14 rounded-2xl bg-white/15 backdrop-blur flex items-center justify-center border border-white/20"><x-icon name="ambulance" class="h-8 w-8 text-white"/></div>
            <div>
                <h1 class="text-2xl sm:text-3xl font-extrabold">🚨 {{ __('Request Emergency Ambulance') }}</h1>
                <p class="text-teal-50 mt-1">{{ __('One-tap SOS. Our team dispatches the nearest ambulance.') }}</p>
            </div>
        </div>
    </div>

    <form method="POST" action="{{ route('emergency.store') }}" class="bg-white rounded-2xl border border-slate-200 p-6 sm:p-8 space-y-5" x-data="{ loading:false }" @submit="loading=true">
        @csrf
        <div class="grid sm:grid-cols-2 gap-4">
            <div>
                <label class="text-sm font-semibold text-slate-700">{{ __('Contact name') }}</label>
                <input name="contact_name" required value="{{ old('contact_name', auth()->user()?->name) }}" class="mt-1 w-full rounded-lg border-slate-300 focus:ring-amber-500 focus:border-amber-500">
            </div>
            <div>
                <label class="text-sm font-semibold text-slate-700">{{ __('Contact phone') }}</label>
                <input name="contact_phone" required value="{{ old('contact_phone', auth()->user()?->phone) }}" class="mt-1 w-full rounded-lg border-slate-300 focus:ring-amber-500 focus:border-amber-500">
            </div>
        </div>
        <div>
            <label class="text-sm font-semibold text-slate-700">{{ __('Exact location') }}</label>
            <input id="loc" name="location" required placeholder="{{ __('Street / landmark') }}" class="mt-1 w-full rounded-lg border-slate-300 focus:ring-amber-500 focus:border-amber-500">
        </div>
        <div class="grid sm:grid-cols-2 gap-4">
            <div>
                <label class="text-sm font-semibold text-slate-700">{{ __('Latitude') }}</label>
                <input id="lat" name="latitude" readonly class="mt-1 w-full rounded-lg border-slate-200 bg-slate-50 text-sm">
            </div>
            <div>
                <label class="text-sm font-semibold text-slate-700">{{ __('Longitude') }}</label>
                <input id="lng" name="longitude" readonly class="mt-1 w-full rounded-lg border-slate-200 bg-slate-50 text-sm">
            </div>
        </div>
        <button type="button" onclick="grabLoc()" class="inline-flex items-center gap-2 text-sm text-teal-600 font-semibold hover:underline"><x-icon name="location" class="h-4 w-4"/> {{ __('Auto-detect my location') }}</button>

        <div>
            <label class="text-sm font-semibold text-slate-700">{{ __('Describe the emergency') }}</label>
            <textarea name="description" rows="3" class="mt-1 w-full rounded-lg border-slate-300 focus:ring-amber-500 focus:border-amber-500" placeholder="{{ __('e.g. chest pain, accident, breathing difficulty') }}"></textarea>
        </div>
        <button :disabled="loading" type="submit" class="w-full px-4 py-3.5 rounded-lg bg-teal-700 text-white font-extrabold hover:bg-teal-800 text-lg flex items-center justify-center gap-2 disabled:opacity-50 shadow-lg shadow-teal-200">
            <x-icon name="ambulance" class="h-6 w-6"/> <span x-text="loading ? 'Dispatching…' : '{{ __('Send SOS') }}'"></span>
        </button>
        <p class="text-xs text-center text-slate-500">{{ __('For immediate threats, also dial 112 / 108.') }}</p>
    </form>
</div>

<script>
function grabLoc(){
    const locBtn = event.currentTarget;
    const originalText = locBtn.innerHTML;
    locBtn.innerHTML = 'Detecting...';
    locBtn.disabled = true;

    if(!navigator.geolocation){ 
        alert('Geolocation not supported'); 
        locBtn.innerHTML = originalText;
        locBtn.disabled = false;
        return; 
    }
    
    navigator.geolocation.getCurrentPosition((pos)=>{
        const lat = pos.coords.latitude;
        const lng = pos.coords.longitude;
        document.getElementById('lat').value = lat.toFixed(6);
        document.getElementById('lng').value = lng.toFixed(6);
        
        // Reverse geocoding using OpenStreetMap Nominatim (Free)
        fetch(`https://nominatim.openstreetmap.org/reverse?format=json&lat=${lat}&lon=${lng}`)
            .then(res => res.json())
            .then(data => {
                if(data.display_name){
                    document.getElementById('loc').value = data.display_name;
                } else {
                    document.getElementById('loc').value = `Lat: ${lat.toFixed(4)}, Lng: ${lng.toFixed(4)}`;
                }
            })
            .catch(() => {
                document.getElementById('loc').value = `Lat: ${lat.toFixed(4)}, Lng: ${lng.toFixed(4)}`;
            })
            .finally(() => {
                locBtn.innerHTML = originalText;
                locBtn.disabled = false;
            });
    }, (err)=>{
        alert('Could not get location: '+err.message);
        locBtn.innerHTML = originalText;
        locBtn.disabled = false;
    }, { enableHighAccuracy: true, timeout: 5000, maximumAge: 0 });
}
</script>
@endsection
