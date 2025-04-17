<?php

namespace App\Http\Middleware;

use Closure;
use Auth;

class IsUnbanned
{
    public function handle($request, Closure $next)
    {
        if (auth()->check() && auth()->user()->banned) {

            $redirect_to = "";
            if(auth()->user()->user_type == 'admin' || auth()->user()->user_type == 'staff'){
                $redirect_to = "login";
            }else{
                $redirect_to = "user.login";
            }

            auth()->logout();

            flash(translate("You are banned"));
            return redirect()->route($redirect_to);
        }

        return $next($request);
    }
}
