<?php

namespace App\Http\Controllers\ITSupport;

use App\Http\Controllers\Controller;
use App\Models\PasswordResetRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class PasswordResetController extends Controller
{
    public function index()
    {
        $requests = PasswordResetRequest::with('user')
            ->where('is_processed', false)
            ->orderBy('created_at', 'desc')
            ->get();

        return view('it-support.password-reset.index', compact('requests'));
    }

    public function process($id)
    {
        $reset = PasswordResetRequest::findOrFail($id);

        // Generate kode acak 8 digit
        $code = strtoupper(Str::random(8));

        // Update password user & SET IS_TEMPORARY_PASSWORD JADI TRUE
        $reset->user->update([
            'password' => Hash::make($code),
            'is_temporary_password' => true, // ⭐ WAJIB BENAR
        ]);

        $reset->update([
            'temporary_password' => $code,
            'is_processed' => true,
        ]);

        return redirect()->back()
            ->with('reset_code', $code)
            ->with('reset_user', $reset->user->name);
    }
}