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
        $ignoreUrls = collect(config('inertia-flash.ignore_urls', ['broadcasting/auth']));
        foreach($ignoreUrls as $url) {
            if (str_contains($request->url(), $url)) {
                return $next($request);
            }
        }

        inertia_flash()->shareToInertia();
        return $next($request);
    }
}
