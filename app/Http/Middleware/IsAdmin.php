<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Http\Middleware\IsAdmin as Middleware;


class IsAdmin
{
    /**
     * Get the path the user should be redirected to when they are not authenticated.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return string|null
     */
    public function handle(Request $request, Closure $next)
    {
        if(!auth()->check() || !auth()->user()->is_admin) {
            abort(403);
        }
        return $next($request);
    }
}
