<?php

/**
 * Nova-Toggle 5 by Almir Hodzic
 * Original: https://github.com/almirhodzic/nova-toggle
 * Copyright (c) 2025 Almir Hodzic
 * MIT License
 */

namespace AlmirHodzic\NovaToggle\Http\Controllers;

use Laravel\Nova\Nova;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;

class ToggleController extends Controller
{
    public function toggle(Request $request, string $resource, string $resourceId): JsonResponse
    {
        $guards = config('nova-toggle.guards', ['web']);
        $hasAccess = collect($guards)->contains(fn($guard) => auth()->guard($guard)->check());

        if (!$hasAccess) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $resourceClass = Nova::resourceForKey($resource);

        if (!$resourceClass) {
            return response()->json(['error' => 'Resource not found'], 404);
        }

        $model = $resourceClass::newModel()->findOrFail($resourceId);

        $attribute = $request->input('attribute');

        if (!$attribute) {
            return response()->json(['error' => 'Attribute required'], 400);
        }

        $newValue = !$model->{$attribute};
        $model->{$attribute} = $newValue;

        Nova::actionEvent()->forResourceUpdate($request->user(), $model)->save();

        $model->save();

        //$label = $model->name ?? $model->label ?? $model->title ?? $resourceClass::singularLabel();

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
