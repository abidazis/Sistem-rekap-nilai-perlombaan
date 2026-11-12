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

        $userRole = strtolower(auth()->user()->role);

        foreach ($roles as $allowedRole) {
            if ($userRole === strtolower($allowedRole)) {
                return $next($request);
            }
        }

        // PERBAIKAN: Lempar ke '/' karena rute dashboard kita ada di '/'
        return redirect('/')->with('error', 'Akses Terbatas! Anda bukan Admin.');
    }
}
