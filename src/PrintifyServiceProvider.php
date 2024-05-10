<?php

namespace Printify;

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
    public function boot()
    {

    }

    /**
     * Configure the publishable resources offered by the package.
     *
     * @return void
     */
    protected function configurePublishing()
    {

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
