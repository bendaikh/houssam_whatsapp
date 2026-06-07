<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RequireCustomDomain
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!$request->attributes->get('resolved_store')) {
            abort(404);
        }

        return $next($request);
    }
}
