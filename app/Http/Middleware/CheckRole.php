<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        // Handle case where roles are passed as a single comma-separated string
        if (count($roles) === 1 && str_contains($roles[0], ',')) {
            $roles = explode(',', $roles[0]);
        }

        // Clean roles and check
        $roles = array_map('trim', array_map('strtolower', $roles));
        $userRole = strtolower($request->user()->role ?? '');

        if (! $request->user() || !in_array($userRole, $roles)) {
            if ($request->is('api/*') || $request->expectsJson()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Unauthorized action.'
                ], 403);
            }
            
            // Jika gagal, tampilkan error 403 agar kita tahu ini masalah role, bukan session hilang
            abort(403, 'Akses ditolak: Anda tidak memiliki peran yang sesuai untuk halaman ini (' . ($request->user()->role ?? 'Guest') . ').');
        }

        return $next($request);
    }
}
