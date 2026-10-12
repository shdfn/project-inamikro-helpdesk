<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function showLogin()
    {
        return view('login');
    }

    public function login(Request $request)
    {
        // Validasi: boleh email ATAU username
        $validator = Validator::make($request->all(), [
            'login' => 'required',
            'password' => 'required',
        ], [
            'login.required' => 'Email / Username Wajib Diisi.',
            'password.required' => 'Kata Sandi Wajib Diisi.',
        ]);

        if ($validator->fails()) {
            return redirect('login')->withErrors($validator)->withInput();
        }

        $loginInput = $request->input('login');
        $password = $request->input('password');

        // Tentukan apakah input berupa email atau username
        $field = filter_var($loginInput, FILTER_VALIDATE_EMAIL) ? 'email' : 'username';

        $credentials = [
            $field => $loginInput,
            'password' => $password,
        ];

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();

            return match (auth()->user()->role) {
                'mentor' => redirect()->route('mentor.dashboard'),
                'it_support' => redirect()->route('it.dashboard'),
                'kepala_it' => redirect()->route('kepala.dashboard'),
                default => redirect('/login'),
            };
        }

        return redirect('login')
            ->withErrors(['login' => 'Email/Username atau Kata Sandi salah.'])
            ->withInput();
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('login');
    }
}