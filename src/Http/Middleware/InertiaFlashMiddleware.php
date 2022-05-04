<?php

namespace Igerslike\InertiaFlash\Http\Middleware;

use Illuminate\Http\Request;
use Illuminate\Http\Response;

class InertiaFlashMiddleware
{
    /**
     * Handle the incoming request.
     *
     * @param Request $request
     * @param callable $next
     *
     * @return Response
     */
    public function handle($request, $next)
    {
        // Sockets and Broadcasting Exclude
        if(str_contains($request->url(),'broadcasting/auth') || $request->has('_inertia_flash')) {
            return $next($request);
        }
        inertia_flash()->shareToInertia();
        return $next($request);
    }
}
