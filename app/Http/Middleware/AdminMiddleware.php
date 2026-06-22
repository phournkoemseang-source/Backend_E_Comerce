<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->bearerToken()) {
            $user = $request->user('sanctum');
            if (!$user || $user->role !== 'admin') {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized. Admin access required.',
                ], 403);
            }
        } elseif (!auth()->check() || auth()->user()->role !== 'admin') {
            return redirect()->route('admin.login');
        }

        return $next($request);
    }
}
