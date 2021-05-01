<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class IPIsValid
{
    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param Closure $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $allowed_ips = explode(',', env('BILLING_SERVER_IPS'));

        abort_unless(
            empty($allowed_ips) || in_array($request->ip(), array_map('trim', $allowed_ips)),
            403,
            "You don't have permission to access the resources."
        );

        return $next($request);
    }
}
