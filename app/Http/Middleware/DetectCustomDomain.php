<?php

namespace App\Http\Middleware;

use App\Support\StoreDomain;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class DetectCustomDomain
{
    public function handle(Request $request, Closure $next): Response
    {
        $store = StoreDomain::resolveFromHost($request->getHost());

        if ($store) {
            $request->attributes->set('resolved_store', $store);
        }

        return $next($request);
    }
}
