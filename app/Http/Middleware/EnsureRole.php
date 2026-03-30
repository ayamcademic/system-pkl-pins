<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureRole
{
    public function handle(Request $request, Closure $next, string $role): Response
    {
        $user = $request->user();

        if (!$user) {
            abort(401);
        }

        // asumsi ada kolom `role` pada table users
        if (($user->role ?? null) !== $role) {
            abort(403, 'Unauthorized role.');
        }

        return $next($request);
    }
}
