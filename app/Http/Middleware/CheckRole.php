<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, $role): Response
    {
        if ($request->user() && auth()->check() && $request->user()->getRoleAttribute() === $role ) {
            return $next($request);
        } else {
            return response()->json([
                'message' => 'Unauthorized access!',
            ], 401);
        }
    }
}
