<?php

namespace MaksimM\ConditionalDebugBar\Http\Middleware;

use Barryvdh\Debugbar\LaravelDebugbar;
use Illuminate\Contracts\Container\Container;

class OptionalDebugBar
{
    /**
     * The App container.
     *
     * @var Container
     */
    protected $container;

    /**
     * The URIs that should be excluded.
     *
     * @var array
     */
    protected $except = [];

    /**
     * Create a new middleware instance.
     *
     * @param Container $container
     */
    public function __construct(Container $container)
    {
        $this->container = $container;
        $this->except = config('debugbar.except') ?: [];
    }

    /**
     * Handle an incoming request.
     *
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param \Closure                                  $next
     *
     * @throws \Exception
     *
     * @return mixed
     */
    public function handle($request, \Closure $next)
    {
        if (class_exists(LaravelDebugbar::class)) {
            //except array option support
            if ($this->inExceptArray($request)) {
                return $next($request);
            }
            $debugBar = resolve(LaravelDebugbar::class);
            $bootValidator = resolve(config('conditional-debugbar.debugbar-boot-validator'));
            $debuggerPreviouslyEnabled = $debugBar->isEnabled();
            if ($debuggerPreviouslyEnabled && !$bootValidator->isInDebugMode()) {
                $debugBar->disable();
                session()->put('debugBarEnabled', false);
            } elseif (!$debuggerPreviouslyEnabled && $bootValidator->isInDebugMode()) {
                $debugBar->enable();
                session()->put('debugBarEnabled', true);
                $response = $next($request);
                return $debugBar->modifyResponse($request, $response);
            }
        }

        return $next($request);
    }

    /**
     * Determine if the request has a URI that should be ignored.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return bool
     */
    protected function inExceptArray($request)
    {
        foreach ($this->except as $except) {
            if ($except !== '/') {
                $except = trim($except, '/');
            }

            if ($request->is($except)) {
                return true;
            }
        }

        return false;
    }
}
