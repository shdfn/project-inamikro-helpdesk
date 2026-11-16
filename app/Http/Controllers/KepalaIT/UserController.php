<?php

namespace App\Http\Controllers\KepalaIT;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\ProfileRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $query = User::query();

        if ($request->filled('role')) {
            $query->where('role', $request->role);
        }

        if ($request->filled('q')) {
            $search = $request->q;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('username', 'like', "%{$search}%");
            });
        }

        $users = $query->orderBy('created_at', 'desc')->get();

        // Ambil data pengajuan profil yang masih Pending
        $profileRequests = ProfileRequest::with('user')
            ->where('status', 'Pending')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('kepala-it.users.index', compact('users', 'profileRequests'));
    }

    public function create()
    {
        return view('kepala-it.users.create');
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'username' => 'required|unique:users,username',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:6',
            'role' => 'required|in:mentor,it_support,kepala_it',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        User::create([
            'name' => $request->name,
            'username' => $request->username,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
        ]);

        return redirect()->route('kepala.users.index')->with('success', 'User berhasil ditambahkan.');
    }

    public function edit($id)
    {
        $user = User::findOrFail($id);
        return view('kepala-it.users.edit', compact('user'));
    }

    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'username' => 'required|unique:users,username,' . $id,
            'email' => 'required|email|unique:users,email,' . $id,
            'role' => 'required|in:mentor,it_support,kepala_it',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $data = [
            'name' => $request->name,
            'username' => $request->username,
            'email' => $request->email,
            'role' => $request->role,
        ];

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $user->update($data);

        return redirect()->route('kepala.users.index')->with('success', 'User berhasil diupdate.');
    }

    public function destroy($id)
    {
        $user = User::findOrFail($id);

        if ($user->id === auth()->id()) {
            return back()->with('error', 'Tidak bisa menghapus akun sendiri.');
        }

        $user->delete();
        return back()->with('success', 'User berhasil dihapus.');
    }

    // ==========================================
    // FITUR PERSETUJUAN PERUBAHAN PROFIL
    // ==========================================

    public function approveProfile($id)
    {
        $req = ProfileRequest::findOrFail($id);
        
        $user = User::findOrFail($req->user_id);
        $user->update([
            'name' => $req->new_name,
            'username' => $req->new_username,
            'email' => $req->new_email,
            'password' => $req->new_password ? $req->new_password : $user->password,
        ]);

        $req->update(['status' => 'Approved']);

        return back()->with('success', 'Perubahan profil berhasil disetujui.');
    }

    public function rejectProfile($id)
    {
        $req = ProfileRequest::findOrFail($id);
        $req->update(['status' => 'Rejected']);
        
        return back()->with('success', 'Pengajuan perubahan profil ditolak.');
    }
}