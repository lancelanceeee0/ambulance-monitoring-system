@extends('layouts.app')

@section('title', 'Dispatch')

@push('styles')
<style>
    .dispatch-input { font-size: 17px; }
    #message { margin: 16px 0 6px 18px; font-weight: 600; min-height: 22px; }
    #message.ok { color: #4078a8; }
    #message.err { color: #c0392b; }
    .result { margin-top: 10px; border: 1px solid #eee; border-radius: 20px; padding: 16px; background: #fcf5f7; display: none; opacity: 1; transition: opacity .6s ease; }
    .result.fading { opacity: 0; }
    .enter-row { display: flex; justify-content: center; margin-top: 14px; }
    .enter-row .btn { margin-top: 0; min-width: 160px; }
    .result .box { background: #fff; border: 1px solid #ddd; border-radius: 30px; padding: 11px 18px; margin-bottom: 10px; font-size: 16px; }
    .result img { width: 100%; max-height: 320px; object-fit: cover; border-radius: 16px; margin-bottom: 10px; display: block; }
    .result-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 0 16px; align-items: start; }
    .result-grid #r-photo { grid-column: 1; grid-row: 1 / span 4; }
    .result-grid .box { grid-column: 2; }
    .result .box:last-child { margin-bottom: 0; }
    .result-grid #r-photo img, .result-grid .no-photo { height: 100%; min-height: 240px; max-height: none; margin-bottom: 0; }
    @media (max-width: 750px) {
        .result-grid { grid-template-columns: 1fr; }
        .result-grid #r-photo, .result-grid .box { grid-column: 1; grid-row: auto; }
        .result-grid #r-photo { margin-bottom: 10px; }
    }
    .no-photo { background: #fff; border: 1px dashed #ccc; border-radius: 16px; padding: 70px 0; margin-bottom: 10px; color: #999; text-align: center; }
</style>
@endpush

@section('content')
<div class="split">
    <div class="panel">
        <h1>Ambulance Dispatch</h1>
        <p>Enter the ambulance plate / unit number and click ENTER to dispatch.</p>
    </div>

    <div class="content">
        <input type="text" id="plate" class="dispatch-input" placeholder="Enter Ambulance Plate / Unit Number" autocomplete="off" autofocus>
        <p class="muted" style="margin:10px 0 0 18px">The cursor is automatically ready for the next plate number.</p>

        <div class="enter-row">
            <button type="button" id="enterBtn" class="btn">ENTER</button>
        </div>

        <div class="actions" style="justify-content:center">
            <a href="/vehicle-registration" class="btn gray">Vehicle Registration</a>
        </div>

        <div id="message"></div>

        <div class="result" id="result">
            <div class="result-grid">
                <div class="box" id="r-driver"></div>
                <div id="r-photo"></div>
                <div class="box" id="r-plate"></div>
                <div class="box" id="r-base"></div>
                <div class="box" id="r-last"></div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    const plate = document.getElementById('plate');
    const message = document.getElementById('message');
    const result = document.getElementById('result');
    const token = document.querySelector('meta[name="csrf-token"]').content;

    const enterBtn = document.getElementById('enterBtn');
    let hideTimer = null;
    let fadeTimer = null;
    let busy = false;

    // The Enter key must never dispatch; only the ENTER button does.
    plate.addEventListener('keydown', (e) => {
        if (e.key === 'Enter') e.preventDefault();
    });

    // Hides the warning/success message and the ambulance profile card together.
    function hideResult() {
        clearTimeout(hideTimer);
        clearTimeout(fadeTimer);
        result.classList.remove('fading');
        result.style.display = 'none';
        message.textContent = '';
        message.className = '';
    }

    // Shows the card (if any) and message, then clears both after 5 seconds.
    function showTemporarily(showCard) {
        clearTimeout(hideTimer);
        clearTimeout(fadeTimer);
        result.classList.remove('fading');
        result.style.display = showCard ? 'block' : 'none';
        hideTimer = setTimeout(() => {
            if (showCard) result.classList.add('fading');
            fadeTimer = setTimeout(hideResult, showCard ? 600 : 0);
        }, 5000);
    }

    enterBtn.addEventListener('click', async () => {
        if (busy) return;

        const value = plate.value.trim();
        if (!value) { plate.focus(); return; }

        busy = true;
        enterBtn.disabled = true;

        try {
            const res = await fetch('/dispatch', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': token
                },
                body: JSON.stringify({ plate_number: value })
            });
            const data = await res.json();

            message.textContent = data.message || 'Something went wrong.';
            message.className = (data.found && data.recorded !== false) ? 'ok' : 'err';

            if (data.found) {
                const a = data.ambulance;
                document.getElementById('r-driver').textContent = a.driver_name;
                document.getElementById('r-plate').textContent = a.plate_number;
                document.getElementById('r-base').textContent = a.base_location;
                document.getElementById('r-last').textContent = 'Last Dispatched: ' + a.last_dispatched;
                document.getElementById('r-photo').innerHTML = a.photo_url
                    ? '<img src="' + a.photo_url + '" alt="Ambulance photo">'
                    : '<div class="no-photo">No photo</div>';
                showTemporarily(true);
            } else {
                showTemporarily(false);
            }
        } catch (err) {
            message.textContent = 'Network error. Please try again.';
            message.className = 'err';
            showTemporarily(false);
        }

        busy = false;
        enterBtn.disabled = false;
        plate.value = '';
        plate.focus();
    });

    // Keep the input focused (e.g. after clicking elsewhere on the page).
    plate.focus();
</script>
@endpush
