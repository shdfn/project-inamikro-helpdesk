<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class ProfileController extends Controller
{
    public function edit()
    {
        return view('profile.edit');
    }

    public function update(Request $request)
    {
        $user = auth()->user();

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'username' => 'required|unique:users,username,' . $user->id,
            
            // ⭐ UBAH DISINI: min:6 menjadi min:8
            'password' => 'nullable|min:8|confirmed',
            
        ], [
            // ⭐ Tambahkan pesan error kustom
            'password.min' => 'Kata sandi minimal harus 8 karakter.',
            'password.confirmed' => 'Konfirmasi kata sandi tidak cocok.',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $data = [
            'name' => $request->name,
            'email' => $request->email,
            'username' => $request->username,
        ];

        // Jika password diisi, update password-nya
        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        // ⭐ KUNCI: Jika request datang dari pop-up modal, hilangkan status temporary
        if ($request->has('clear_temporary')) {
            $data['is_temporary_password'] = false;
        }

        $user->update($data);

        // Jika berasal dari pop-up, arahkan ke dashboard dengan pesan sukses
        if ($request->has('clear_temporary')) {
            // Perbaikan: Arahkan ke dashboard sesuai role user (bukan hanya mentor)
            $route = match ($user->role) {
                'mentor' => 'mentor.dashboard',
                'it_support' => 'it.dashboard',
                'kepala_it' => 'kepala.dashboard',
                default => 'login',
            };
            
            return redirect()->route($route)
                ->with('success', 'Kata sandi berhasil diperbarui. Akun Anda sekarang aman.');
        }

        return back()->with('success', 'Profil berhasil diupdate.');
    }
}