@extends('layouts.app')

@section('title', 'Vehicle Registration')

@section('content')
<div class="split">
    <div class="panel">
        <h1>Vehicle Registration</h1>
        <p>Register the ambulance and driver information and upload a JPEG photo.</p>
    </div>

    <div class="content">
        @if (session('success'))
            <div class="success">{{ session('success') }}</div>
        @endif

        @if ($errors->any())
            <div class="errors">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="/vehicle-registration" method="POST" enctype="multipart/form-data" class="stack">
            @csrf

            <label>Driver First Name</label>
            <input type="text" name="driver_first_name" value="{{ old('driver_first_name') }}" required>

            <label>Driver Last Name</label>
            <input type="text" name="driver_last_name" value="{{ old('driver_last_name') }}" required>

            <label>Plate Number / Unit Number</label>
            <input type="text" name="plate_number" value="{{ old('plate_number') }}" placeholder="e.g. 2026-0001" required>

            <label>Base Location</label>
            <input type="text" name="base_location" value="{{ old('base_location') }}" placeholder="e.g. Brgy. Hall - Cabuyao" required>

            <label>Upload Ambulance Photo (JPEG/JPG, max 2 MB)</label>
            <input type="file" name="photo" accept=".jpg,.jpeg,image/jpeg" required>

            <div class="actions" style="margin-top:20px">
                <button type="submit" class="btn">Submit</button>
                <a href="/dispatch" class="btn gray">Cancel</a>
            </div>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-head">
        <h2>Registered Ambulances</h2>
    </div>
    <div class="card-body">
        <div class="table-scroll">
            <table>
                <thead>
                    <tr><th>Plate Number</th><th>Driver Name</th><th>Base Location</th></tr>
                </thead>
                <tbody>
                    @forelse ($ambulances as $a)
                        <tr>
                            <td>{{ $a->plate_number }}</td>
                            <td>{{ $a->driver_name }}</td>
                            <td>{{ $a->base_location }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="3">No ambulances registered yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
