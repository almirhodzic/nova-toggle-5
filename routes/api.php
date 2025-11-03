<?php

/**
 * Nova-Toggle 5 by Almir Hodzic
 * Original: https://github.com/almirhodzic/nova-toggle
 * Copyright (c) 2025 Almir Hodzic
 * MIT License
 */

use AlmirHodzic\NovaToggle\Http\Controllers\ToggleController;
use Illuminate\Support\Facades\Route;

Route::post('/{resource}/{resourceId}', [ToggleController::class, 'toggle'])
    ->name('nova-toggle.toggle');
