<?php

namespace App\Http\Middleware;

use Closure;
use Auth;

class AdminCheckStatus
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $flag = false;
        if(! $request->user()->role->status)
        {
            $message = 'Your role status has been disabled.';
            $flag = true;
        }
        else if(! $request->user()->status) {
            $message = 'Your account status has been disabled.';
            $flag = true;
        }

        if($flag)
        {
            Auth::guard('admin')->logout();
            return redirect()->route('admin.auth.login')->withErrors(['error' => $message]);
        }

        return $next($request);
    }
}
