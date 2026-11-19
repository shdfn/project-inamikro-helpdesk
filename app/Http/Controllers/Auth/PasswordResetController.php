<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\PasswordResetRequest;
use App\Helpers\NotificationHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PasswordResetController extends Controller
{
    /**
     * Tampilkan form "Lupa Sandi"
     */
    public function showForgotForm()
    {
        return view('auth.forgot-password');
    }

    /**
     * Proses permintaan reset password (kirim ke IT Support)
     */
    public function sendResetLink(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|exists:users,email',
        ], [
            'email.required' => 'Email Wajib Diisi.',
            'email.email' => 'Format Email Tidak Valid.',
            'email.exists' => 'Email tidak terdaftar.',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $user = User::where('email', $request->email)->first();

        // Buat permintaan reset (hindari duplikasi jika sudah ada yang belum diproses)
        PasswordResetRequest::firstOrCreate(
            ['user_id' => $user->id, 'is_processed' => false],
            ['temporary_password' => null]
        );

        // Kirim notifikasi ke semua IT Support
        $itSupports = User::where('role', 'it_support')->get();
        foreach ($itSupports as $it) {
            NotificationHelper::send(
                $it->id,
                null, 
                $user->id,
                'reset_password',
                'Permintaan Reset Password',
                $user->name . ' meminta reset password. Silakan buka menu Reset Password.'
            );
        }

        // Kembalikan ke halaman login dengan pesan sukses
        return redirect()->route('login')
            ->with('status', 'Permintaan reset telah dikirim ke IT Support. Silakan hubungi IT Support untuk mendapatkan kode sandi sementara.');
    }
}