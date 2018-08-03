<?php

namespace MaksimM\ConditionalDebugBar\Http\Middleware;

use Barryvdh\Debugbar\LaravelDebugbar;
use Closure;
use Illuminate\Http\Request;

class DebugBarEnabled
{

    /**
     * Handle an incoming request.
     *
     * @param  Request $request
     * @param  Closure $next
     * @return mixed
     * @throws \Exception
     */
    public function handle($request, Closure $next)
    {
        if (class_exists(\Debugbar::class)) {
            $debugBar = resolve(LaravelDebugbar::class);
            if (session()->has('debugBarEnabled'))
                $debugBar->enable();
            if (!$debugBar->isEnabled()) {
                abort(404);
            }
        }
        return $next($request);

    }
}