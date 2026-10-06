@extends('layouts.app')

@section('title', 'Ambulance Arrival')

@push('styles')
<style>
    .arrival-toolbar { margin-top: 18px; }
    .arrival-toolbar input { width: 100%; }
    .section-title { margin: 28px 0 6px; color: #444; }
    .section-note { color: #666; font-size: 14px; margin: 0; }
    .arrival-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 18px; margin-top: 18px; }
    .arrival-card { border: 1px solid #eee; border-radius: var(--radius); padding: 20px; background: #fff; box-shadow: 0 5px 18px rgba(0,0,0,.08); }
    .arrival-card h3 { margin: 0 0 12px; color: #4078a8; }
    .arrival-info { margin: 8px 0; font-size: 14px; }
    .countdown { font-size: 32px; font-weight: 700; text-align: center; margin: 18px 0 6px; color: #4078a8; letter-spacing: 1px; }
    .countdown-label { text-align: center; color: #666; font-size: 13px; }
    .status { display: inline-block; padding: 5px 12px; border-radius: 30px; background: #fde8ed; color: #bd466e; font-weight: 700; font-size: 13px; }
    .arrival-card.returned { border-color: #16a34a; background: #f0fdf4; }
    .arrival-card.returned .status { background: #dcfce7; color: #166534; }
    .return-btn { width: 100%; margin-top: 16px; }
    .return-btn:disabled { opacity: .65; cursor: default; }
    .available-card { border: 1px solid #b7e4c7; border-radius: var(--radius); padding: 20px; background: #f0fdf4; box-shadow: 0 5px 18px rgba(0,0,0,.06); }
    .available-card h3 { margin: 0 0 12px; color: #166534; }
    .available-badge { display: inline-block; padding: 6px 12px; border-radius: 30px; background: #dcfce7; color: #166534; font-weight: 700; font-size: 13px; }
    .empty { text-align: center; padding: 35px 15px; color: #666; border: 2px dashed #ccc; border-radius: var(--radius); margin-top: 18px; }
    .hidden-by-search { display: none; }
    .estimate { text-align: center; color: #666; font-size: 13px; margin-top: 5px; }
</style>
@endpush

@section('content')
<div class="card">
    <div class="card-head">
        <h2>Ambulance Arrival</h2>
        <p>Monitor dispatched ambulances and confirm when they actually return to base.</p>
    </div>

    <div class="card-body">
        <div class="arrival-toolbar">
            <input type="text" id="arrivalSearch" placeholder="Search ambulance by plate, driver, base, or dispatcher..." autocomplete="off">
        </div>

        <h3 class="section-title">On Mission / Returning</h3>
        <p class="section-note">The timer is only an estimated arrival time. When the ambulance is physically back at base, click <strong>Mark as Returned to Base</strong>.</p>

        @if ($missions->isEmpty())
            <div class="empty" id="noMissions">No ambulances are currently on mission.</div>
        @else
            <div class="arrival-grid" id="arrivalGrid">
                @foreach ($missions as $mission)
                    <div class="arrival-card"
                         data-search="{{ strtolower($mission->plate_number . ' ' . $mission->driver_name . ' ' . $mission->base_location . ' ' . $mission->dispatched_by_name) }}">
                        <h3>{{ $mission->plate_number }}</h3>
                        <div class="arrival-info"><strong>Driver:</strong> {{ $mission->driver_name }}</div>
                        <div class="arrival-info"><strong>Base:</strong> {{ $mission->base_location }}</div>
                        <div class="arrival-info"><strong>Dispatched By:</strong> {{ $mission->dispatched_by_name }}</div>
                        <div class="arrival-info"><strong>Status:</strong> <span class="status">{{ $mission->status }}</span></div>

                        <div class="countdown" data-countdown data-arrival="{{ $mission->arrival_at?->toIso8601String() }}">--:--</div>
                        <div class="countdown-label">Estimated time until back to base</div>
                        @if ($mission->arrival_at)
                            <div class="estimate">Estimated arrival: {{ $mission->arrival_at->format('M j, Y h:i A') }}</div>
                        @endif

                        <button type="button" class="btn return-btn" data-return data-log-id="{{ $mission->id }}">
                            Mark as Returned to Base
                        </button>
                    </div>
                @endforeach
            </div>
        @endif

        <h3 class="section-title" id="available-for-dispatch">Available for Dispatch</h3>
        <p class="section-note">Ambulances that are back at their base, including newly registered vehicles.</p>

        @if ($available->isEmpty())
            <div class="empty" id="noAvailable">No ambulances are currently available for dispatch.</div>
        @else
            <div class="arrival-grid" id="availableGrid">
                @foreach ($available as $ambulance)
                    @php $last = $ambulance->lastLog; @endphp
                    <div class="available-card" data-available-search="{{ strtolower($ambulance->plate_number . ' ' . $ambulance->driver_name . ' ' . $ambulance->base_location . ' ' . ($last?->dispatched_by_name ?? '')) }}">
                        <h3>{{ $ambulance->plate_number }}</h3>
                        <div class="arrival-info"><strong>Driver:</strong> {{ $ambulance->driver_name }}</div>
                        <div class="arrival-info"><strong>Base:</strong> {{ $ambulance->base_location }}</div>
                        @if ($last)
                            <div class="arrival-info"><strong>Last Dispatched By:</strong> {{ $last->dispatched_by_name }}</div>
                            <div class="arrival-info"><strong>Last Dispatch:</strong> {{ \Carbon\Carbon::parse($last->date . ' ' . $last->time)->format('F j, Y h:i:s A') }}</div>
                        @else
                            <div class="arrival-info"><strong>Last Dispatch:</strong> No dispatch history yet</div>
                        @endif
                        <span class="available-badge">Available for Dispatch</span>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</div>
@endsection

@push('scripts')
<script>
    const token = document.querySelector('meta[name="csrf-token"]').content;

    function formatCountdown(seconds) {
        seconds = Math.max(0, seconds);
        const hours = Math.floor(seconds / 3600);
        const minutes = Math.floor((seconds % 3600) / 60);
        const secs = seconds % 60;

        if (hours > 0) {
            return String(hours).padStart(2, '0') + ':' +
                   String(minutes).padStart(2, '0') + ':' +
                   String(secs).padStart(2, '0');
        }

        return String(minutes).padStart(2, '0') + ':' + String(secs).padStart(2, '0');
    }

    function updateCountdowns() {
        document.querySelectorAll('[data-countdown]').forEach(countdown => {
            const arrival = countdown.dataset.arrival;
            if (!arrival) return;

            const seconds = Math.ceil((new Date(arrival).getTime() - Date.now()) / 1000);
            countdown.textContent = seconds > 0 ? formatCountdown(seconds) : '00:00';
        });
    }

    document.querySelectorAll('[data-return]').forEach(button => {
        button.addEventListener('click', async () => {
            if (button.disabled) return;

            const logId = button.dataset.logId;
            button.disabled = true;
            button.textContent = 'Updating...';

            try {
                const res = await fetch('/ambulance-arrival/' + logId + '/complete', {
                    method: 'POST',
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': token
                    }
                });
                const data = await res.json();

                if (data.success && data.status === 'Available') {
                    button.closest('.arrival-card').classList.add('returned');
                    button.closest('.arrival-card').querySelector('.status').textContent = 'Available';
                    button.textContent = 'Returned to Base ✓';

                    // Reload so the confirmed ambulance moves to Available for Dispatch.
                    setTimeout(() => window.location.reload(), 500);
                } else {
                    button.disabled = false;
                    button.textContent = 'Mark as Returned to Base';
                    alert(data.message || 'Unable to update ambulance status.');
                }
            } catch (error) {
                button.disabled = false;
                button.textContent = 'Mark as Returned to Base';
                alert('Network error. Please try again.');
            }
        });
    });

    const searchInput = document.getElementById('arrivalSearch');
    searchInput.addEventListener('input', function () {
        const term = this.value.trim().toLowerCase();

        document.querySelectorAll('.arrival-card').forEach(card => {
            card.classList.toggle('hidden-by-search', !card.dataset.search.includes(term));
        });

        document.querySelectorAll('.available-card').forEach(card => {
            card.classList.toggle('hidden-by-search', !card.dataset.availableSearch.includes(term));
        });
    });

    updateCountdowns();
    setInterval(updateCountdowns, 1000);
</script>
@endpush
