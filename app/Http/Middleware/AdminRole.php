<?php

namespace App\Http\Middleware;

use Closure;
use Auth;

class AdminRole
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
        if(Auth::User()->role==1)
            return $next($request);
        else if(Auth::User()->role==2) {
            if(count(Auth::User()->courseReviewer)>0)
                return $next($request);
            else
                return redirect()->route('homepage')->with('danger-message','You have not been assigned a course to manage yet, please contact the System Administrator. Thank you!');
        }
            
        return redirect()->route('homepage')->with('danger-message','You are not allowed to access this page!');
    }
}
