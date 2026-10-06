<?php

namespace App\Http\Controllers;

use App\Models\Ambulance;
use Illuminate\Http\Request;

class AmbulanceController extends Controller
{
    public function create()
    {
        $ambulances = Ambulance::latest()->get();

        return view('ambulances.register', compact('ambulances'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'driver_first_name' => 'required|string|max:100',
            'driver_last_name' => 'required|string|max:100',
            'plate_number' => 'required|string|max:50|unique:ambulances,plate_number',
            'base_location' => 'required|string|max:150',
            'photo' => 'required|file|mimes:jpeg,jpg|max:2048', // 2 MB
        ]);

        // Saved straight into public/uploads/ambulances (no storage:link needed)
        $file = $request->file('photo');
        $filename = time() . '_' . preg_replace('/[^A-Za-z0-9_-]/', '', $data['plate_number']) . '.jpg';
        $file->move(public_path('uploads/ambulances'), $filename);

        $data['photo_path'] = 'uploads/ambulances/' . $filename;
        unset($data['photo']);

        // New vehicles start at base, so they are immediately available.
        $data['status'] = 'back_to_base';

        Ambulance::create($data);

        return redirect('/vehicle-registration')->with('success', 'Ambulance registered successfully.');
    }
}
