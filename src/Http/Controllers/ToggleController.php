<?php

/**
 * Nova-Toggle 5 by Almir Hodzic
 * Original: https://github.com/almirhodzic/nova-toggle-5
 * Copyright (c) 2025 Almir Hodzic
 * MIT License
 */

namespace AlmirHodzic\NovaToggle5\Http\Controllers;

use AlmirHodzic\NovaToggle5\Toggle;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Nova;

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
     * Authentication against the viewNova gate is enforced by the nova:api middleware
     * group. This method additionally enforces resource-level update authorization and
     * restricts writable attributes to those declared as Toggle fields on the resource.
     *
     * @param NovaRequest $request The Nova-aware HTTP request containing attribute and labelKey
     * @param string $resource The Nova resource key (e.g., 'users', 'posts')
     * @param string $resourceId The ID of the resource/model to update
     * @return JsonResponse JSON response with success status, new value, and label
     */
    public function toggle(NovaRequest $request, string $resource, string $resourceId): JsonResponse
    {
        // Resolve the Nova resource class from the resource key
        $resourceClass = Nova::resourceForKey($resource);

        if (! $resourceClass) {
            return response()->json(['error' => 'Resource not found'], 404);
        }

        // Find the model instance by ID (throws 404 if not found)
        $query = $resourceClass::newModel()->newQuery();

        if ($resourceClass::softDeletes()) {
            $query->withTrashed();
        }

        $model = $query->findOrFail($resourceId);

        // Wrap the model in the Nova resource so we can evaluate authorization
        // and field definitions exactly as Nova would in its own index/update flow.
        $novaResource = new $resourceClass($model);

        if (! $novaResource->authorizedToUpdate($request)) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        // Get the attribute name to toggle (e.g., 'is_active', 'show')
        $attribute = $request->input('attribute');

        if (! $attribute) {
            return response()->json(['error' => 'Attribute required'], 400);
        }

        // Whitelist: only attributes exposed via a Toggle field on this resource may be
        // changed through this endpoint. This prevents callers from targeting arbitrary
        // boolean columns (e.g. is_admin) that were never meant to be toggled here, and
        // respects per-field visibility/readonly rules.
        $toggleField = collect($novaResource->availableFields($request))
            ->first(fn ($field) => $field instanceof Toggle
                && $field->attribute === $attribute
                && ! $field->isReadonly($request)
            );

        if (! $toggleField) {
            return response()->json(['error' => 'Attribute not toggleable'], 403);
        }

        // Toggle the boolean value
        $newValue = ! $model->{$attribute};
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
            'label' => $label,
        ]);
    }
}
