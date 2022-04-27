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
        inertia_flash()->shareToInertia();
        return $next($request);
    }
}
