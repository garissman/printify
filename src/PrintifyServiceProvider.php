<?php

namespace Garissman\Printify;

use Illuminate\Support\ServiceProvider;

class PrintifyServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom(__DIR__ . '/../stubs/printify.php', 'printify');

        $this->app->bind(\Printify\Printify::class, function ($app) {
            return new \Printify\Printify($app);
        });

        $this->app->alias(\Printify\Printify::class, 'printify');
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->configurePublishing();
    }

    /**
     * Configure the publishable resources offered by the package.
     *
     * @return void
     */
    protected function configurePublishing()
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__ . '/../stubs/printify.php' => config_path('printify.php'),
            ], 'printify-config');
        }
    }

    /**
     * Configure the routes offered by the application.
     *
     * @return void
     */
    protected function configureRoutes()
    {

    }

    /**
     * Register the package's commands.
     */
    protected function registerCommands(): void
    {
    }

    /**
     * Register the response bindings.
     *
     * @return void
     */
    protected function registerResponseBindings()
    {

    }
}
