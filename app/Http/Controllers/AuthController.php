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
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required',
        ], [
            'email.required' => 'Email Wajib Diisi.',
            'email.email' => 'Format Email Tidak Valid.',
            'password.required' => 'Kata Sandi Wajib Diisi.',
        ]);

        if ($validator->fails()) {
            return redirect('login')->withErrors($validator)->withInput();
        }

        $credentials = $request->only('email', 'password');

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();

            // Redirect berdasarkan role
            return match (auth()->user()->role) {
                'mentor' => redirect()->route('mentor.dashboard'),
                'it_support' => redirect()->route('it.dashboard'),
                'kepala_it' => redirect()->route('kepala.dashboard'),
                default => redirect('/login'),
            };
        }

        return redirect('login')->withErrors(['email' => 'Email atau Kata Sandi salah.'])->withInput();
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('login');
    }
}