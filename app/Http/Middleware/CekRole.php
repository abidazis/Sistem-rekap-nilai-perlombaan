<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CekRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, ...$roles)
    {
        if (!auth()->check()) {
            return redirect('/login');
        }

        $userRole = strtolower(auth()->user()->role ?? '');

        foreach ($roles as $allowedRole) {
            if ($userRole === strtolower($allowedRole)) {
                return $next($request);
            }
        }

        // Arahkan ke halaman yang memang boleh diakses role tersebut (hindari redirect loop).
        $fallback = match ($userRole) {
            'admin', 'juri' => '/',
            'rekap' => '/input-nilai',
            default => '/login',
        };

        // Kalau halaman tujuan juga ditolak, hentikan dengan 403.
        if ($request->is(ltrim($fallback, '/'))) {
            abort(403, 'Akses Terbatas!');
        }

        return redirect($fallback)->with('error', 'Akses Terbatas! Halaman ini tidak tersedia untuk peran Anda.');
    }
}
