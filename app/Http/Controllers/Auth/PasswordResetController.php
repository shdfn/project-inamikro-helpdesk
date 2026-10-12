<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Carbon\Carbon;

class PasswordResetController extends Controller
{
    // Tampilkan form "Lupa Sandi"
    public function showForgotForm()
    {
        return view('auth.forgot-password');
    }

    // Proses kirim token reset
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

        // Generate token
        $token = Str::random(64);

        // Simpan / update di password_reset_tokens
        DB::table('password_reset_tokens')->updateOrInsert(
            ['email' => $request->email],
            [
                'token' => Hash::make($token),
                'created_at' => Carbon::now(),
            ]
        );

        // ⚠️ Untuk demo, kita tampilkan token di halaman
        // Di produksi, token ini dikirim via email
        return redirect()->route('password.reset', ['token' => $token, 'email' => $request->email])
            ->with('status', 'Token reset berhasil dibuat. Silakan isi sandi baru.');
    }

    // Tampilkan form reset password
    public function showResetForm(Request $request, $token)
    {
        return view('auth.reset-password', [
            'token' => $token,
            'email' => $request->email,
        ]);
    }

    // Proses reset password
    public function resetPassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'token' => 'required',
            'email' => 'required|email|exists:users,email',
            'password' => 'required|min:6|confirmed',
        ], [
            'email.required' => 'Email Wajib Diisi.',
            'email.exists' => 'Email tidak terdaftar.',
            'password.required' => 'Kata Sandi Baru Wajib Diisi.',
            'password.min' => 'Kata Sandi minimal 6 karakter.',
            'password.confirmed' => 'Konfirmasi Kata Sandi tidak cocok.',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        // Cek token
        $reset = DB::table('password_reset_tokens')->where('email', $request->email)->first();

        if (!$reset) {
            return back()->withErrors(['email' => 'Token tidak ditemukan. Silakan request ulang.']);
        }

        if (!Hash::check($request->token, $reset->token)) {
            return back()->withErrors(['token' => 'Token tidak valid.']);
        }

        // Cek expired (60 menit)
        if (Carbon::parse($reset->created_at)->addMinutes(60)->isPast()) {
            return back()->withErrors(['token' => 'Token sudah kadaluarsa. Silakan request ulang.']);
        }

        // Update password user
        User::where('email', $request->email)->update([
            'password' => Hash::make($request->password),
        ]);

        // Hapus token
        DB::table('password_reset_tokens')->where('email', $request->email)->delete();

        return redirect()->route('login')->with('status', 'Kata sandi berhasil diubah. Silakan login dengan sandi baru.');
    }
}