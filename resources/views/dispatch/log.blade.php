@extends('layouts.app')

@section('title', 'Dispatch Log')

@push('styles')
<style>
    .log-tools { display: flex; flex-wrap: wrap; gap: 10px; align-items: center; justify-content: space-between; margin-bottom: 16px; }
    .filters { display: flex; gap: 8px; flex-wrap: wrap; }
    .filters a { padding: 8px 20px; border-radius: 30px; border: 1px solid #ddd; background: #fff; color: #bd466e; font-weight: 700; font-size: 14px; text-decoration: none; }
    .filters a.active { background: #bd466e; color: #fff; border-color: #bd466e; }
    .search-form { display: flex; gap: 8px; }
    .search-form input { padding: 9px 16px; border: 1px solid #ddd; border-radius: 30px; font-size: 14px; font-family: inherit; }
    .search-form button { padding: 9px 20px; border: none; border-radius: 30px; background: #bd466e; color: #fff; font-weight: 700; cursor: pointer; font-family: inherit; }
    .badge.ongoing { background: #ffedd5; color: #c2410c; }
    .badge.finished { background: #dcfce7; color: #166534; }
    tr.row-ongoing td:first-child { border-left: 5px solid #f97316; }
    tr.row-finished td:first-child { border-left: 5px solid #22c55e; }
</style>
@endpush

@section('content')
<div class="card">
    <div class="card-head">
        <h2>Dispatch Log</h2>
        <p>Every dispatch is recorded as its own entry.</p>
    </div>

    <div class="card-body">
        <div class="log-tools">
            <div class="filters">
                <a href="/dispatch-log?status=all&plate={{ urlencode($search) }}" class="{{ $filter === 'all' ? 'active' : '' }}">All</a>
                <a href="/dispatch-log?status=ongoing&plate={{ urlencode($search) }}" class="{{ $filter === 'ongoing' ? 'active' : '' }}">Ongoing</a>
                <a href="/dispatch-log?status=finished&plate={{ urlencode($search) }}" class="{{ $filter === 'finished' ? 'active' : '' }}">Finished</a>
            </div>

            <form method="GET" action="/dispatch-log" class="search-form">
                <input type="hidden" name="status" value="{{ $filter }}">
                <input type="text" name="plate" value="{{ $search }}" placeholder="Search plate number">
                <button type="submit">Search</button>
            </form>
        </div>

        <div class="table-scroll">
            <table>
                <thead>
                    <tr>
                        <th>Plate Number</th>
                        <th>Driver Name</th>
                        <th>Base Location</th>
                        <th>Dispatched</th>
                        <th>Returned</th>
                        <th>Duration</th>
                        <th>Dispatched By</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($logs as $log)
                        <tr class="{{ $log->is_ongoing ? 'row-ongoing' : 'row-finished' }}">
                            <td>{{ $log->plate_number }}</td>
                            <td>{{ $log->driver_name }}</td>
                            <td>{{ $log->base_location }}</td>
                            <td>{{ $log->dispatched_at->format('m/d/Y h:i A') }}</td>
                            <td>{{ $log->returned_at ? $log->returned_at->format('m/d/Y h:i A') : '--' }}</td>
                            <td>{{ $log->duration ?? '--' }}</td>
                            <td>{{ $log->dispatched_by_name }}</td>
                            <td>
                                @if ($log->is_ongoing)
                                    <span class="badge ongoing">ONGOING</span>
                                @else
                                    <span class="badge finished">FINISHED</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="8">No dispatch records found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
