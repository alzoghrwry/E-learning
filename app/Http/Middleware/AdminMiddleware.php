<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class AdminMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        if ($request->user()?->role !== 'admin') {
            return response()->json([
                'status' => 403,
                'message' => 'ليس لديك الصلاحية للوصول إلى هذه الصفحة'
            ], 403);
        }

        return $next($request);
    }
}
