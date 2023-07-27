<?php

namespace App\Http\Middleware;

use Closure;

class RedirectToInstall
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
        if (!file_exists(storage_path('installed'))) {
            if ($request->is('install') || $request->is('install/*')) return $next($request);
            if ($request->wantsJson()) {
                return response('Application need to be installed properly', 404);
            }
            return redirect('install');
        }
        return $next($request);
    }
}
