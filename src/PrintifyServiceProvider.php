<?php

namespace Garissman\Printify;

use Garissman\Printify\Console\Commands\RegisterPrintifyWebhooks;
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

        $this->app->bind(Printify::class, function ($app) {
            return new Printify($app);
        });

        $this->app->alias(Printify::class, 'printify');
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot(): void
    {
        $this->configurePublishing();
        $this->registerCommands();
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
     * Register the package's commands.
     */
    protected function registerCommands(): void
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                RegisterPrintifyWebhooks::class
            ]);
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
     * Register the response bindings.
     *
     * @return void
     */
    protected function registerResponseBindings()
    {

    }
}
