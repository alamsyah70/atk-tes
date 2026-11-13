<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    public function index()
    {
        return view('Login.index', [
            'title' => 'Login'
        ]);
    }

    public function authenticate(Request $request)
    {
        $credentials = $request->validate([
            'username' => 'required',
            'password' => 'required',
        ]);

        // Kolom database adalah "user",
        // sedangkan input form adalah "username".
        $loginCredentials = [
            'user' => $credentials['username'],
            'password' => $credentials['password'],
        ];

        if (Auth::attempt($loginCredentials)) {

            $request->session()->regenerate();

            $user = Auth::user();

            // Tolak user yang belum diberikan role.
            if (!$user->hasAnyRole([
                'Super Admin',
                'Admin Pusat',
                'Admin Cabang',
                'Petugas Gudang',
                'Viewer',
            ])) {
                Auth::logout();

                $request->session()->invalidate();
                $request->session()->regenerateToken();

                return back()->with(
                    'loginError',
                    'Akun belum memiliki role.'
                );
            }

            // Dashboard pusat
            if ($user->hasAnyRole([
                'Super Admin',
                'Admin Pusat',
            ])) {
                return redirect()->intended(
                    '/dashboard-admin'
                );
            }

            // Dashboard cabang
            return redirect()->intended(
                '/dashboard'
            );
        }

        return back()
            ->with('loginError', 'Username atau password salah!')
            ->onlyInput('username');
    }

    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
}
