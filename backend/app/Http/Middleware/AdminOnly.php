<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class AdminOnly
{
    public function handle(Request $request, Closure $next)
    {
        $u = $request->user();
        if (!$u || !$u->is_admin) {
            if ($request->expectsJson()) return response()->json(['message' => 'Unauthenticated'], 401);
            return redirect()->guest(route('admin.login'));
        }
        return $next($request);
    }
}
