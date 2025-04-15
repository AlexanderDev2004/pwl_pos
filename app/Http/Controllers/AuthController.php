<?php

namespace App\Http\Controllers;

use App\Models\LevelModel;
use App\Models\UserModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function register()
    {
        $levels = LevelModel::all();
        return view('auth.regis', compact('levels'));
    }

    public function postregister(Request $request)
    {
        $request->validate([
            'username' => 'required|min:3|max:20',
            'password' => 'required|min:6|max:20',
            'nama' => 'required|max:100',
            'level_id' => 'required',
        ]);

        if (UserModel::where('username', $request->username)->exists()) {
            return response()->json([
                'status' => false,
                'message' => 'Username telah digunakan'
            ], 400);
        }

        UserModel::create([
            'username' => $request->username,
            'password' => bcrypt($request->password),
            'nama' => $request->nama,
            'level_id' => $request->level_id,
        ]);

        return response()->json([
            'status' => true,
            'message' => 'User berhasil ditambahkan',
            'redirect' => route('login')
        ]);
    }

    public function login()
    {
        if (Auth::check()) {
            return redirect('/');
        }
        return view('auth.login');
    }

    public function postlogin(Request $request)
    {
        if ($request->ajax() || $request->wantsJson()) {
            $credentials = $request->only('username', 'password');

            if (Auth::attempt($credentials)) {
                return response()->json([
                    'status' => true,
                    'message' => 'Login Berhasil',
                    'redirect' => url('/')
                ]);
            }

            return response()->json([
                'status' => false,
                'message' => 'Login Gagal'
            ]);
        }

        return redirect('login');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('login');
    }

    public function profile($id)
    {
        $breadcrumb = (object)[
            'title' => 'Profil',
            'list' => ['Home', 'Profil']
        ];
        $active_menu = 'profile';
        $user = UserModel::findOrFail($id);
        return view('auth.profile', compact('breadcrumb', 'user', 'active_menu'));
    }

    public function edit($id)
    {
        $breadcrumb = (object)[
            'title' => 'Edit Profil',
            'list' => ['Home', 'Profil', 'Edit Profil']
        ];
        $active_menu = 'profile';
        $user = UserModel::findOrFail($id);

        return view('auth.edit_profile', compact('breadcrumb', 'user', 'active_menu'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'nama' => 'required|max:100',
            'username' => 'required|min:3|max:20',
            'password' => 'nullable|min:6|max:20|same:password_confirmation',
        ]);

        $user = UserModel::findOrFail($id);

        if (UserModel::where('username', $request->username)->where('user_id', '!=', $id)->exists()) {
            return redirect()->back()->withErrors(['username' => 'Username telah digunakan']);
        }

        $user->nama = $request->nama;
        $user->username = $request->username;

        if ($request->password) {
            $user->password = bcrypt($request->password);
        }

        if ($request->hasFile('profile_image')) {
            $image = $request->file('profile_image');
            $extension = $image->getClientOriginalExtension();
            $filename = 'user_' . $user->user_id . '.' . $extension;
            $image->move(public_path('admin'), $filename);
            // Tidak menyimpan ke database
            $user->avatar = $filename;
        }

        $user->save();

        return redirect("/profile/{$id}")->with('success', 'Profile updated successfully!');
    }
}
