<?php

namespace MaksimM\ConditionalDebugBar;

use Barryvdh\Debugbar\Middleware\DebugbarEnabled;
use Illuminate\Routing\Route;
use Illuminate\Routing\Router;
use Illuminate\Support\Collection;
use Illuminate\Support\ServiceProvider;

class ConditionalDebugBarServiceProvider extends ServiceProvider
{

    /**
     * Bootstrap the application services.
     * @throws \Exception
     */
    public function boot()
    {
        if ($this->app->runningInConsole()) {
            if (!str_contains($this->app->version(), 'Lumen')) {
                $this->publishes([
                    __DIR__ . '/../config/conditional-debugbar.php' => config_path('addons/conditional-debugbar.php'),
                ], 'config');
            }
        }
        $this->overrideDebugBarMiddleware($this->app);
    }

    /**
     * @throws \Exception
     */
    public function overrideDebugBarMiddleware($app)
    {
        if (class_exists(\Debugbar::class)) {
            /**
             * @var Router $router
             */
            $router = $app['router'];
            /**
             * @var Collection|Route[] $debugbarRoutes
             */
            $debugbarRoutes = collect($router->getRoutes()->getRoutesByName())
                ->filter(function ($value, $key) {
                    return strpos($key, 'debugbar') !== false;
                });
            if ($debugbarRoutes->count() > 0) {
                session()->put('debugBarEnabled', 1);
                foreach ($debugbarRoutes as $route) {
                    $action = $route->getAction();
                    $action['middleware'] = array_merge(array_diff($action['middleware'], ['web', DebugbarEnabled::class]), ['web', Http\Middleware\DebugBarEnabled::class]);
                    $route->setAction($action);
                }
            }
        }
    }

    /**
     * Register the application services.
     */
    public function register()
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/conditional-debugbar.php', 'addons.conditional-debugbar');
    }

}