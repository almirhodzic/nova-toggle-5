<?php

/**
 * Nova-Toggle 5 by Almir Hodzic
 * Original: https://github.com/almirhodzic/nova-toggle-5
 * Copyright (c) 2025 Almir Hodzic
 * MIT License
 */

namespace AlmirHodzic\NovaToggle5\Http\Controllers;

use Laravel\Nova\Nova;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;

/**
 * Controller for handling toggle field state changes
 *
 * Manages AJAX requests from the frontend toggle component to update
 * boolean field values and return appropriate responses with labels
 */
class ToggleController extends Controller
{
    /**
     * Toggle a boolean attribute on a Nova resource
     *
     * This endpoint handles the toggling of boolean fields directly from the index view.
     * It performs authentication checks, validates the resource and attribute,
     * updates the model, logs the action event, and returns the new state with label.
     *
     * @param Request $request The HTTP request containing attribute and labelKey
     * @param string $resource The Nova resource key (e.g., 'users', 'posts')
     * @param string $resourceId The ID of the resource/model to update
     * @return JsonResponse JSON response with success status, new value, and label
     *
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException If resource not found
     */
    public function toggle(Request $request, string $resource, string $resourceId): JsonResponse
    {
        // Check authentication against configured guards
        $guards = config('nova-toggle.guards', ['web']);
        $hasAccess = collect($guards)->contains(fn($guard) => auth()->guard($guard)->check());

        if (!$hasAccess) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        // Resolve the Nova resource class from the resource key
        $resourceClass = Nova::resourceForKey($resource);

        if (!$resourceClass) {
            return response()->json(['error' => 'Resource not found'], 404);
        }

        // Find the model instance by ID (throws 404 if not found)
        $model = $resourceClass::newModel()->findOrFail($resourceId);

        // Get the attribute name to toggle (e.g., 'is_active', 'show')
        $attribute = $request->input('attribute');

        if (!$attribute) {
            return response()->json(['error' => 'Attribute required'], 400);
        }

        // Toggle the boolean value
        $newValue = !$model->{$attribute};
        $model->{$attribute} = $newValue;

        // Log the update action in Nova's action events
        Nova::actionEvent()->forResourceUpdate($request->user(), $model)->save();

        // Persist the change to database
        $model->save();

        // Determine the label to display in toast notification
        // Priority: custom labelKey → name → label → title → resource singular label
        $labelKey = $request->input('labelKey', null);
        $label = $labelKey && isset($model->{$labelKey})
            ? $model->{$labelKey}
            : ($model->name ?? $model->label ?? $model->title ?? $resourceClass::singularLabel());

        return response()->json([
            'success' => true,
            'value' => $newValue,
            'label' => $label
        ]);
    }
}
