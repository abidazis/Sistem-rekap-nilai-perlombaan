<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    public function index()
    {
        return view('login');
    }

    public function authenticate(Request $request)
    {
        $credentials = $request->validate([
            'username' => ['required'],
            'password' => ['required'],
        ]);

        // Karena config/auth.php antum sudah diarahkan ke tabel Juri,
        // Auth::attempt akan otomatis ngecek username & password ke tabel juri!
        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();
            
            // Redirect aman ke dashboard
            return redirect()->intended('/');
        }

        // Kalau password salah, balikin ke halaman login bawa pesan error
        return back()->withErrors([
            'username' => '⚠️ Username atau Password antum salah bro!',
        ])->onlyInput('username');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        
        return redirect('/login');
    }
}