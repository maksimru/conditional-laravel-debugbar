<?php

namespace MaksimM\ConditionalDebugBar\Http\Middleware;

use Barryvdh\Debugbar\LaravelDebugbar;
use Exception;
use Illuminate\Contracts\Container\Container;
use Illuminate\Contracts\Debug\ExceptionHandler;
use Illuminate\Http\Request;

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
            $debugBar = resolve(LaravelDebugbar::class);
            $bootValidator = resolve(config('conditional-debugbar.debugbar-boot-validator'));
            $debuggerPreviouslyEnabled = $debugBar->isEnabled();
            if ($debuggerPreviouslyEnabled && !$bootValidator->isInDebugMode()) {
                $debugBar->disable();
                session()->put('debugBarEnabled', false);
            } elseif (!$debuggerPreviouslyEnabled && $bootValidator->isInDebugMode()) {
                $debugBar->enable();
                session()->put('debugBarEnabled', true);
                try {
                    /** @var \Illuminate\Http\Response $response */
                    $response = $next($request);
                } catch (Exception $e) {
                    $response = $this->handleException($request, $e);
                }

                return $debugBar->modifyResponse($request, $response);
            }
        }

        return $next($request);
    }

    /**
     * Handle the given exception.
     *
     * (Copy from Illuminate\Routing\Pipeline by Taylor Otwell)
     *
     * @param $passable
     * @param Exception $e
     *
     * @throws Exception
     *
     * @return mixed
     */
    protected function handleException($passable, Exception $e)
    {
        if (!$this->container->bound(ExceptionHandler::class) || !$passable instanceof Request) {
            throw $e;
        }

        $handler = $this->container->make(ExceptionHandler::class);

        $handler->report($e);

        return $handler->render($passable, $e);
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
