<?php

/**
 * Nova-Toggle 5 by Almir Hodzic
 * Original: https://github.com/almirhodzic/nova-toggle
 * Copyright (c) 2025 Almir Hodzic
 * MIT License
 */

namespace AlmirHodzic\NovaToggle;

use Laravel\Nova\Fields\Boolean;
use Laravel\Nova\Fields\Filters\BooleanFilter;
use Laravel\Nova\Fields\Field;
use Laravel\Nova\Http\Requests\NovaRequest;
use Illuminate\Http\Request;

class Toggle extends Field
{
    public $component = 'nova-toggle';

    protected $onColor = '#00d5be';
    protected $onColorDark = '#009689';
    protected $offColor = '#e5e5e5';
    protected $offColorDark = '#323f57';
    protected $onBulletColor = '#ffffff';
    protected $onBulletColorDark = '#ffffff';
    protected $offBulletColor = '#ffffff';
    protected $offBulletColorDark = '#ffffff';
    protected $hideWhenCallback = null;
    protected $helpOnIndex = null;
    protected $helpOnForm = null;
    protected $helpOnDetail = null;
    protected $readonlyWhenCallback = null;
    protected $onLabel = null;
    protected $offLabel = null;
    protected $onLabelColor = '#ffffff';
    protected $onLabelColorDark = '#ffffff';
    protected $offLabelColor = '#a1a1a1';
    protected $offLabelColorDark = '#737373';
    protected $toastShow = true;
    protected $toastLabelKey = null;
    protected $shouldBeFilterable = false;

    public function __construct($name, $attribute = null, ?callable $resolveCallback = null)
    {
        parent::__construct($name, $attribute, $resolveCallback);
    }

    public function onBullet(string $light, ?string $dark = null): self
    {
        $this->onBulletColor = $light;
        $this->onBulletColorDark = $dark ?? $light;
        return $this;
    }

    public function offBullet(string $light, ?string $dark = null): self
    {
        $this->offBulletColor = $light;
        $this->offBulletColorDark = $dark ?? $light;
        return $this;
    }

    public function onColor(string $light, ?string $dark = null): self
    {
        $this->onColor = $light;
        $this->onColorDark = $dark ?? $light;
        return $this;
    }

    public function offColor(string $light, ?string $dark = null): self
    {
        $this->offColor = $light;
        $this->offColorDark = $dark ?? $light;
        return $this;
    }

    public function hideWhen(callable $callback): self
    {
        $this->hideWhenCallback = $callback;
        return $this;
    }

    public function helpOnIndex(string $text): self
    {
        $this->helpOnIndex = $text;
        return $this;
    }

    public function helpOnForm(string $text): self
    {
        $this->helpOnForm = $text;
        return $this;
    }

    public function helpOnDetail(string $text): self
    {
        $this->helpOnDetail = $text;
        return $this;
    }

    public function readonlyWhen(callable $callback): self
    {
        $this->readonlyWhenCallback = $callback;
        return $this;
    }

    public function valueLabelText(?string $onLabel = 'ON', ?string $offLabel = 'OFF'): self
    {
        $this->onLabel = $onLabel;
        $this->offLabel = $offLabel;
        return $this;
    }

    public function valueLabelOnColors(string $light, ?string $dark = null): self
    {
        $this->onLabelColor = $light;
        $this->onLabelColorDark = $dark ?? $light;
        return $this;
    }

    public function valueLabelOffColors(string $light, ?string $dark = null): self
    {
        $this->offLabelColor = $light;
        $this->offLabelColorDark = $dark ?? $light;
        return $this;
    }

    public function toastLabelKey(string $key): self
    {
        $this->toastLabelKey = $key;
        return $this;
    }

    public function toastShow(bool $show = true): self
    {
        $this->toastShow = $show;
        return $this;
    }

    /**
     * Make the field filterable.
     */
    public function filterable(): self
    {
        $this->shouldBeFilterable = true;
        return $this;
    }

    /**
     * Return the filterable attribute for the field.
     */
    public function serializeForFilter()
    {
        if ($this->shouldBeFilterable) {
            // Create a Boolean field for filtering
            return Boolean::make($this->name, $this->attribute)
                ->filterable()
                ->serializeForFilter();
        }

        return parent::serializeForFilter();
    }

    /**
     * Define the filterable callback.
     */
    protected function makeFilter(NovaRequest $request)
    {
        if ($this->shouldBeFilterable) {
            return new BooleanFilter($this->attribute);
        }

        return null;
    }

    public function jsonSerialize(): array
    {
        $request = app(NovaRequest::class);
        $hidden = false;
        $readonly = $this->isReadonly($request);

        if ($this->hideWhenCallback && is_callable($this->hideWhenCallback)) {
            $hidden = call_user_func($this->hideWhenCallback, $request, $this->resource ?? null);
        }

        if (!$readonly && $this->readonlyWhenCallback && is_callable($this->readonlyWhenCallback)) {
            $readonly = call_user_func($this->readonlyWhenCallback, $request, $this->resource ?? null);
        }

        if (!$readonly) {
            $guards = config('nova-toggle.guards', ['web']);
            $hasAccess = collect($guards)->contains(fn($guard) => auth()->guard($guard)->check());
            $readonly = !$hasAccess;
        }

        return array_merge(parent::jsonSerialize(), [
            'readonly' => $readonly,
            'hidden' => $hidden,
            'onColor' => $this->onColor,
            'onColorDark' => $this->onColorDark,
            'offColor' => $this->offColor,
            'offColorDark' => $this->offColorDark,
            'onBulletColor' => $this->onBulletColor,
            'onBulletColorDark' => $this->onBulletColorDark,
            'offBulletColor' => $this->offBulletColor,
            'offBulletColorDark' => $this->offBulletColorDark,
            'helpOnIndex' => $this->helpOnIndex,
            'helpOnForm' => $this->helpOnForm,
            'helpOnDetail' => $this->helpOnDetail,
            'onLabel' => $this->onLabel,
            'offLabel' => $this->offLabel,
            'onLabelColor' => $this->onLabelColor,
            'onLabelColorDark' => $this->onLabelColorDark,
            'offLabelColor' => $this->offLabelColor,
            'offLabelColorDark' => $this->offLabelColorDark,
            'toastShow' => $this->toastShow,
            'toastLabelKey' => $this->toastLabelKey,
        ]);
    }
}
