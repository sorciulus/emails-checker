<?php

namespace sorciulus\EmailChecker\Laravel;

use sorciulus\EmailChecker\EmailChecker;
use Illuminate\Support\ServiceProvider;

class EmailCheckerServiceProvider extends ServiceProvider
{
    /**
     * Register bindings in the container.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton(EmailChecker::class, function ($app) {
            return new EmailChecker();
        });
    }
}