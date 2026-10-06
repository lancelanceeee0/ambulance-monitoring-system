<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Ambulance extends Model
{
    protected $fillable = [
        'driver_first_name',
        'driver_last_name',
        'plate_number',
        'base_location',
        'photo_path',
        'status',
    ];

    protected $attributes = [
        'status' => 'back_to_base',
    ];

    // "Bern1, Rey1" format (Last, First) like the reference image
    public function getDriverNameAttribute()
    {
        return $this->driver_last_name . ', ' . $this->driver_first_name;
    }
}
