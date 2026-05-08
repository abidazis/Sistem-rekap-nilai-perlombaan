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

        $posisi = strtolower(auth()->user()->posisi);
        
        foreach ($roles as $role) {
            if (str_contains($posisi, strtolower($role))) {
                return $next($request);
            }
        }

        return redirect('/dashboard')->with('error', 'Akses Terbatas!');
    }
}
