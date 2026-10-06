<?php

namespace App\Http\Controllers;

use App\Models\Ambulance;
use App\Models\DispatchLog;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DispatchController extends Controller
{
    // Main dispatch page (input only)
    public function index()
    {
        return view('dispatch.index');
    }

    // Enter plate > Enter > card + timestamp
    public function record(Request $request)
    {
        $request->validate(['plate_number' => 'required|string']);

        $plate = trim($request->plate_number);
        $ambulance = Ambulance::where('plate_number', $plate)->first();

        if (!$ambulance) {
            return response()->json([
                'found' => false,
                'message' => 'Ambulance not found. Please register it first.',
            ], 404);
        }

        $now = Carbon::now();
        $photoUrl = $ambulance->photo_path ? asset($ambulance->photo_path) : null;

        // The latest record is the source of truth for whether this ambulance
        // is currently away from base. It must be manually marked as returned.
        $existing = DispatchLog::where('plate_number', $ambulance->plate_number)
            ->latest('id')
            ->first();

        if ($existing && $existing->status === 'On Mission') {
            return response()->json([
                'found' => true,
                'recorded' => false,
                'message' => 'Ambulance is still on mission. Mark it as Returned to Base first.',
                'ambulance' => $this->card($ambulance, $photoUrl, Carbon::parse($existing->date . ' ' . $existing->time)),
            ]);
        }

        // Estimated arrival only. Actual return is confirmed by the user
        // through the "Mark as Returned to Base" button on the arrival page.
        $estimatedArrival = $now->copy()->addMinutes(5);

        DispatchLog::create([
            'date' => $now->toDateString(),
            'time' => $now->format('H:i:s'),
            'arrival_at' => $estimatedArrival,
            'plate_number' => $ambulance->plate_number,
            'driver_name' => $ambulance->driver_name,
            'base_location' => $ambulance->base_location,
            'dispatched_by_id' => Auth::id(),
            'dispatched_by_name' => Auth::user()->full_name,
            'status' => 'On Mission',
        ]);

        $ambulance->update(['status' => 'on_mission']);

        return response()->json([
            'found' => true,
            'recorded' => true,
            'message' => 'Dispatch recorded. Estimated arrival time is ' . $estimatedArrival->format('h:i A') . '.',
            'ambulance' => $this->card($ambulance, $photoUrl, $now),
        ]);
    }

    // Dispatch Log page
    public function logs()
    {
        $filter = request('status', 'all');
        $search = trim((string) request('plate', ''));

        $query = DispatchLog::query();

        if ($filter === 'ongoing') {
            $query->where('status', 'On Mission')->whereNull('returned_at');
        } elseif ($filter === 'finished') {
            $query->whereNotNull('returned_at');
        }

        if ($search !== '') {
            $query->where('plate_number', 'like', '%' . $search . '%');
        }

        // Every dispatch is its own row, newest first.
        $logs = $query->orderByDesc('date')->orderByDesc('time')->orderByDesc('id')->get();

        return view('dispatch.log', compact('logs', 'filter', 'search'));
    }

    // Ambulance Arrival / Back to Base page
    public function arrival()
    {
        $missions = DispatchLog::where('status', 'On Mission')
            ->orderBy('arrival_at')
            ->get();

        // Available = every registered ambulance that is not currently on a
        // mission. A vehicle with no dispatch history counts as back_to_base,
        // so newly registered vehicles show up immediately.
        $onMissionPlates = $missions->pluck('plate_number')->all();

        $available = Ambulance::whereNotIn('plate_number', $onMissionPlates)
            ->orderByDesc('id')
            ->get()
            ->each(function ($ambulance) {
                $ambulance->lastLog = DispatchLog::where('plate_number', $ambulance->plate_number)
                    ->latest('id')
                    ->first();
            });

        return view('dispatch.arrival', compact('missions', 'available'));
    }

    // The estimated countdown does NOT automatically mark the ambulance as
    // returned. The dispatcher confirms the real-world return with a button.
    public function completeArrival(DispatchLog $log)
    {
        if ($log->status !== 'On Mission') {
            return response()->json([
                'success' => false,
                'status' => $log->status,
                'message' => 'This ambulance has already been marked as returned.',
            ]);
        }

        $log->update([
            'status' => 'Available',
            // Actual return time; arrival_at stays as the original estimate.
            'returned_at' => Carbon::now(),
        ]);

        Ambulance::where('plate_number', $log->plate_number)
            ->update(['status' => 'back_to_base']);

        return response()->json([
            'success' => true,
            'status' => $log->fresh()->status,
        ]);
    }

    private function card(Ambulance $a, ?string $photoUrl, Carbon $when): array
    {
        return [
            'driver_name' => $a->driver_name,
            'photo_url' => $photoUrl,
            'plate_number' => $a->plate_number,
            'base_location' => $a->base_location,
            'last_dispatched' => $when->format('F j, Y h:i:s A'),
        ];
    }
}
