<?php

namespace App\Http\Middleware;

use App\Models\BlockedIp;
use App\Models\Store;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckBlockedIp
{
    public function handle(Request $request, Closure $next): Response
    {
        $store = $request->attributes->get('resolved_store');

        if (!$store) {
            $subdomain = $request->route('subdomain');

            if (!$subdomain) {
                return $next($request);
            }

            $store = Store::where('subdomain', $subdomain)
                ->where('is_active', true)
                ->first();

            if (!$store) {
                return $next($request);
            }
        }

        $clientIp = $request->ip();

        $isBlocked = BlockedIp::where('store_id', $store->id)
            ->where('ip_address', $clientIp)
            ->exists();

        if ($isBlocked) {
            return response()->view('access-denied', [], 503);
        }

        return $next($request);
    }
}
