<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class UserController extends Controller
{
    public function showLogin()
    {
        return view('home');
    }

    public function showRegister()
    {
        return view('register');
    }

    public function login(Request $request)
    {
        $incomingFields = $request->validate([
            'loginname' => 'required',
            'loginpassword' => 'required'
        ]);

        if (auth()->attempt([
            'name' => $incomingFields['loginname'],
            'password' => $incomingFields['loginpassword']
        ])) {
            $request->session()->regenerate();
            return redirect('/dispatch');
        }

        return back()->withErrors([
            'loginname' => 'Invalid username or password.'
        ])->onlyInput('loginname');
    }

    public function logout(Request $request)
    {
        auth()->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }

    public function register(Request $request)
    {
        $incomingFields = $request->validate([
            'full_name' => ['required', 'string', 'max:100'],

            'name' => [
                'required',
                'min:3',
                'max:10',
                Rule::unique('users', 'name')
            ],

            'email' => [
                'required',
                'email',
                Rule::unique('users', 'email')
            ],

            // 8 chars, upper + lower, number, special character
            'password' => [
                'required',
                'confirmed',
                'max:200',
                Password::min(8)->mixedCase()->numbers()->symbols(),
            ],
        ]);

        $incomingFields['password'] = bcrypt($incomingFields['password']);

        $user = User::create($incomingFields);

        auth()->login($user);

        return redirect('/dispatch');
    }
}
