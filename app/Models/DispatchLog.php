<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class DispatchLog extends Model
{
    protected $casts = [
        'arrival_at' => 'datetime',
        'returned_at' => 'datetime',
    ];

    protected $fillable = [
        'date',
        'time',
        'arrival_at',
        'returned_at',
        'plate_number',
        'driver_name',
        'base_location',
        'dispatched_by_id',
        'dispatched_by_name',
        'status',
    ];

    public function dispatcher()
    {
        return $this->belongsTo(User::class, 'dispatched_by_id');
    }

    // Date + time of the dispatch.
    public function getDispatchedAtAttribute(): Carbon
    {
        return Carbon::parse($this->date . ' ' . $this->time);
    }

    // ONGOING = still on mission and not yet returned.
    public function getIsOngoingAttribute(): bool
    {
        return $this->status === 'On Mission' && $this->returned_at === null;
    }

    // Human readable duration, e.g. "1h 30m". Null while ongoing.
    public function getDurationAttribute(): ?string
    {
        if (!$this->returned_at) {
            return null;
        }

        $minutes = max(0, $this->dispatched_at->diffInMinutes($this->returned_at));
        $h = intdiv($minutes, 60);
        $m = $minutes % 60;

        return $h > 0 ? "{$h}h {$m}m" : "{$m}m";
    }
}
