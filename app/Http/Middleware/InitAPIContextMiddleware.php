<?php

namespace App\Http\Middleware;

use App\Helpers\API\Context;
use Closure;
use Illuminate\Support\Facades\App;

class InitAPIContextMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        App::setLocale($request->header('Language', 'en'));
        if ($request->isMethod('GET')) {
            Context::setCurrency($request->header('Currency', 'USD'));
        } else {
            Context::setCurrency(null);
        }
        return $next($request);
    }
}
