<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RedirectBasedOnRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string $redirectType = 'default'): Response
    {
        // Jika user sudah login
        if (auth()->check()) {
            $user = auth()->user();

            switch ($redirectType) {
                case 'after_login':
                    // Redirect setelah login berdasarkan role
                    if ($user->hasRole('Pengunjung')) {
                        return redirect()->route('landingpage.home');
                    }

                    if ($user->hasRole('Admin')) {
                        return redirect()->route('admin.dashboard');
                    }

                    // Default redirect untuk role lain
                    return redirect()->route('landingpage.home');

                case 'dashboard_access':
                    // Cek akses ke dashboard
                    if ($user->hasRole('Pengunjung')) {
                        return redirect()->route('landingpage.home')->with('error', 'Anda tidak memiliki akses ke dashboard admin.');
                    }
                    break;

                case 'admin_only':
                    // Hanya admin yang bisa akses
                    if (!$user->hasRole('Admin')) {
                        return redirect()->route('landingpage.home')->with('error', 'Akses ditolak. Hanya admin yang dapat mengakses halaman ini.');
                    }
                    break;

                default:
                    // Default behavior - tidak melakukan redirect
                    break;
            }
        }

        return $next($request);
    }
}
