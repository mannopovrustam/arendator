<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class BackendMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response {
        // avtorizatsiyadan o'tgan foydalanuvchini user_type = 1 bo'lsa, backendga kirishiga ruxsat beramiz
        if (auth()->check() && auth()->user()->user_type != 1) {
            abort(403, 'Unauthorized');
        }
        return $next($request);
    }
}
