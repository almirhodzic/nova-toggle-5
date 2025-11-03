<?php

/**
 * Nova-Toggle 5 by Almir Hodzic
 * Original: https://github.com/almirhodzic/nova-toggle
 * Copyright (c) 2025 Almir Hodzic
 * MIT License
 */

namespace AlmirHodzic\NovaToggle;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use Laravel\Nova\Events\ServingNova;
use Laravel\Nova\Http\Middleware\Authenticate;
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
     * Sets up the toggle endpoint with appropriate middleware and authentication.
     * Routes are prefixed with 'nova-vendor/nova-toggle/toggle' and protected
     * by web middleware and authentication guards
     *
     * @return void
     */
    protected function routes(): void
    {
        // Get the configured authentication guards
        $guards = $this->collectGuardsFromFields();

        // Register routes with middleware and authentication
        Route::middleware(['web', 'auth:' . implode(',', $guards)])
            ->prefix('nova-vendor/nova-toggle/toggle')
            ->group(__DIR__ . '/../routes/api.php');
    }

    /**
     * Collect authentication guards from configuration
     *
     * Retrieves the guards that should be used for authenticating toggle requests.
     * Falls back to 'web' guard if no configuration is present.
     *
     * @return array Array of guard names (e.g., ['web', 'admin'])
     */
    private function collectGuardsFromFields(): array
    {
        return config('nova-toggle.guards', ['web']);
    }
}
