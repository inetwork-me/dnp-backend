<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class PreventDatabaseAction
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
        if (env('DEMO_MODE') == 'On') {
            if ($request->isMethod('post') || $request->isMethod('put') || $request->isMethod('patch') || $request->isMethod('delete')) {
                flash(translate('Data chaning action is not allowed in demo mode.'))->warning();
                return redirect()->back();
                // return redirect()->route('admin.dashboard');
            }
        }
        return $next($request);
    }
}
