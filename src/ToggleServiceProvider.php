<?php

/**
 * Nova-Toggle 5 by Almir Hodzic
 * Original: https://github.com/almirhodzic/nova-toggle-5
 * Copyright (c) 2025 Almir Hodzic
 * MIT License
 */

namespace AlmirHodzic\NovaToggle5;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use Laravel\Nova\Events\ServingNova;
use Laravel\Nova\Nova;

/**
 * Service Provider for Nova Toggle Field
 *
 * Registers the toggle field's assets (JavaScript and CSS) with Nova
 * and sets up the API routes for handling toggle state changes
 */
class ToggleServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any package services
     *
     * Registers the field's assets when Nova is serving and sets up
     * the API routes after the application has booted
     *
     * @return void
     */
    public function boot(): void
    {
        // Register assets when Nova is being served
        Nova::serving(function (ServingNova $event) {
            // Register the compiled JavaScript component
            Nova::script('nova-toggle', __DIR__ . '/../dist/js/toggle.js');
        });

        // Register routes after the application has fully booted
        $this->app->booted(function () {
            $this->routes();
        });
    }

    /**
     * Register the package's API routes
     *
     * Sets up the toggle endpoint behind Nova's own API middleware stack
     * (config('nova.api_middleware')), which enforces the viewNova gate
     * via Laravel\Nova\Http\Middleware\Authorize. Resource- and field-level
     * authorization is enforced additionally in the controller.
     *
     * @return void
     */
    protected function routes(): void
    {
        Route::middleware(['nova:api'])
            ->prefix('nova-vendor/nova-toggle/toggle')
            ->group(__DIR__ . '/../routes/api.php');
    }
}
