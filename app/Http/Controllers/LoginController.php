<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class LoginController extends Controller
{
    public function index()
    {
        return view('login');
    }

    public function authenticate(Request $request)
    {
        $request->validate([
            'username' => ['required'],
            'password' => ['required'],
        ]);

        $identifier = trim((string) $request->input('username'));

        // Terima login pakai username ATAU email
        $user = User::where('username', $identifier)
            ->orWhere('email', $identifier)
            ->first();

        if ($user && Hash::check($request->password, $user->password)) {
            Auth::login($user);
            $request->session()->regenerate();
            return redirect()->intended($this->homePathFor($user));
        }

        return back()->withErrors([
            'username' => '⚠️ Username atau Password antum salah bro!',
        ])->onlyInput('username');
    }

    /**
     * Halaman pertama setelah login sesuai role.
     * Tim Rekap langsung diarahkan ke input nilai (tidak punya akses dashboard).
     */
    private function homePathFor(User $user): string
    {
        return strtolower((string) $user->role) === 'rekap' ? '/input-nilai' : '/';
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login');
    }
}