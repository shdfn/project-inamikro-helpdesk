<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ProfileRequest;
use App\Models\User;
use App\Helpers\NotificationHelper;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class ProfileController extends Controller
{
    // Menampilkan form edit profil
    public function edit()
    {
        return view('profile.edit');
    }

    // Menyimpan pengajuan perubahan ke database
    public function update(Request $request)
    {
        $user = Auth::user();
        
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'username' => 'required|string|max:255|unique:users,username,' . $user->id,
            'email' => 'required|email|unique:users,email,' . $user->id,
            'password' => 'nullable|min:6|confirmed',
        ], [
            'password.min' => 'Kata sandi minimal 6 karakter.',
            'password.confirmed' => 'Konfirmasi kata sandi tidak cocok.',
            'username.unique' => 'Username ini sudah dipakai oleh pengguna lain.',
            'email.unique' => 'Email ini sudah dipakai oleh pengguna lain.',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        // Cek apakah sudah ada pengajuan yang pending
        $exists = ProfileRequest::where('user_id', $user->id)->where('status', 'Pending')->exists();
        if ($exists) {
            return back()->with('error', 'Anda masih memiliki pengajuan yang menunggu persetujuan Kepala IT.');
        }

        // Siapkan data pengajuan
        $data = [
            'user_id' => $user->id,
            'new_name' => $request->name,
            'new_username' => $request->username,
            'new_email' => $request->email,
        ];

        if ($request->filled('password')) {
            $data['new_password'] = Hash::make($request->password);
        }

        // Simpan pengajuan ke database
        ProfileRequest::create($data);

        // ⭐ Kirim Notifikasi ke Kepala IT
        $kepalaIT = User::where('role', 'kepala_it')->first();
        if ($kepalaIT) {
            NotificationHelper::send(
                $kepalaIT->id,                             // Penerima (Kepala IT)
                null,                                      // Ticket ID (kosong karena ini bukan tiket)
                auth()->id(),                              // Pengirim (Mentor/IT)
                'profile_request',                         // Tipe notifikasi
                'Pengajuan Perubahan Profil',
                auth()->user()->name . ' mengajukan perubahan profil. Silakan tinjau di Manajemen User.'
            );
        }

        return back()->with('success', 'Pengajuan perubahan profil berhasil dikirim. Menunggu konfirmasi Kepala IT.');
    }
}