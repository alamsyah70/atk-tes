<?php

namespace App\Http\Middleware;

use App\Models\UserClass;
use Closure;
use Illuminate\Http\Request;

class CheckClassCode
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        $user = $request->user();

    if ($user) {
        $userClass = UserClass::where('user_id', $user->id)->first();

        if ($userClass) {
            // logic to allow access
            return $next($request);
        }
    }

    return redirect('/dashboard')->with('error', 'Masukkan kode yang valid.');
    }
}
