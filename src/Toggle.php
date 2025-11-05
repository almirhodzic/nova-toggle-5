<?php

/**
 * Nova-Toggle 5 by Almir Hodzic
 * Original: https://github.com/almirhodzic/nova-toggle-5
 * Copyright (c) 2025 Almir Hodzic
 * MIT License
 */

namespace AlmirHodzic\NovaToggle5;

use Laravel\Nova\Fields\Boolean;
use Laravel\Nova\Fields\Filters\BooleanFilter;
use Laravel\Nova\Fields\Field;
use Laravel\Nova\Http\Requests\NovaRequest;

/**
 * Toggle Field for Laravel Nova 5
 *
 * Provides a customizable toggle switch with support for:
 * - Light and dark mode colors
 * - Custom labels
 * - Toast notifications
 * - Conditional visibility and readonly states
 * - Filter support
 */
class Toggle extends Field
{
    /**
     * The field's component name
     *
     * @var string
     */
    public $component = 'nova-toggle';

    /**
     * Background color when toggle is ON (light mode)
     *
     * @var string
     */
    protected $onColor = '#00d5be';

    /**
     * Background color when toggle is ON (dark mode)
     *
     * @var string
     */
    protected $onColorDark = '#009689';

    /**
     * Background color when toggle is OFF (light mode)
     *
     * @var string
     */
    protected $offColor = '#e5e5e5';

    /**
     * Background color when toggle is OFF (dark mode)
     *
     * @var string
     */
    protected $offColorDark = '#323f57';

    /**
     * Bullet (circle) color when toggle is ON (light mode)
     *
     * @var string
     */
    protected $onBulletColor = '#ffffff';

    /**
     * Bullet (circle) color when toggle is ON (dark mode)
     *
     * @var string
     */
    protected $onBulletColorDark = '#ffffff';

    /**
     * Bullet (circle) color when toggle is OFF (light mode)
     *
     * @var string
     */
    protected $offBulletColor = '#ffffff';

    /**
     * Bullet (circle) color when toggle is OFF (dark mode)
     *
     * @var string
     */
    protected $offBulletColorDark = '#ffffff';

    /**
     * Callback to determine if field should be hidden
     *
     * @var callable|null
     */
    protected $hideWhenCallback = null;

    /**
     * Help text displayed on index view
     *
     * @var string|null
     */
    protected $helpOnIndex = null;

    /**
     * Help text displayed on form view
     *
     * @var string|null
     */
    protected $helpOnForm = null;

    /**
     * Help text displayed on detail view
     *
     * @var string|null
     */
    protected $helpOnDetail = null;

    /**
     * Callback to determine if field should be readonly
     *
     * @var callable|null
     */
    protected $readonlyWhenCallback = null;

    /**
     * Label text displayed when toggle is ON
     *
     * @var string|null
     */
    protected $onLabel = null;

    /**
     * Label text displayed when toggle is OFF
     *
     * @var string|null
     */
    protected $offLabel = null;

    /**
     * Label text color when toggle is ON (light mode)
     *
     * @var string
     */
    protected $onLabelColor = '#ffffff';

    /**
     * Label text color when toggle is ON (dark mode)
     *
     * @var string
     */
    protected $onLabelColorDark = '#ffffff';

    /**
     * Label text color when toggle is OFF (light mode)
     *
     * @var string
     */
    protected $offLabelColor = '#a1a1a1';

    /**
     * Label text color when toggle is OFF (dark mode)
     *
     * @var string
     */
    protected $offLabelColorDark = '#737373';

    /**
     * Whether to show toast notification on toggle
     *
     * @var bool
     */
    protected $toastShow = true;

    /**
     * Model attribute key to use for toast label
     * Falls back to: name → label → title → resource singular label
     *
     * @var string|null
     */
    protected $toastLabelKey = null;

    /**
     * Whether the field should be filterable
     *
     * @var bool
     */
    protected $shouldBeFilterable = false;

    /**
     * Create a new Toggle field instance
     *
     * @param string $name Field display name
     * @param string|null $attribute Database attribute name
     * @param callable|null $resolveCallback Custom resolve callback
     */
    public function __construct($name, $attribute = null, ?callable $resolveCallback = null)
    {
        parent::__construct($name, $attribute, $resolveCallback);
    }

    /**
     * Set the bullet (circle) color when toggle is ON
     *
     * @param string $light Color for light mode
     * @param string|null $dark Color for dark mode (defaults to light color)
     * @return self
     */
    public function onBullet(string $light, ?string $dark = null): self
    {
        $this->onBulletColor = $light;
        $this->onBulletColorDark = $dark ?? $light;
        return $this;
    }

    /**
     * Set the bullet (circle) color when toggle is OFF
     *
     * @param string $light Color for light mode
     * @param string|null $dark Color for dark mode (defaults to light color)
     * @return self
     */
    public function offBullet(string $light, ?string $dark = null): self
    {
        $this->offBulletColor = $light;
        $this->offBulletColorDark = $dark ?? $light;
        return $this;
    }

    /**
     * Set the background color when toggle is ON
     *
     * @param string $light Color for light mode
     * @param string|null $dark Color for dark mode (defaults to light color)
     * @return self
     */
    public function onColor(string $light, ?string $dark = null): self
    {
        $this->onColor = $light;
        $this->onColorDark = $dark ?? $light;
        return $this;
    }

    /**
     * Set the background color when toggle is OFF
     *
     * @param string $light Color for light mode
     * @param string|null $dark Color for dark mode (defaults to light color)
     * @return self
     */
    public function offColor(string $light, ?string $dark = null): self
    {
        $this->offColor = $light;
        $this->offColorDark = $dark ?? $light;
        return $this;
    }

    /**
     * Hide the field based on a callback condition
     *
     * @param callable $callback Receives (NovaRequest $request, $resource)
     * @return self
     */
    public function hideWhen(callable $callback): self
    {
        $this->hideWhenCallback = $callback;
        return $this;
    }

    /**
     * Set help text to display on the index view
     *
     * @param string $text Help text content
     * @return self
     */
    public function helpOnIndex(string $text): self
    {
        $this->helpOnIndex = $text;
        return $this;
    }

    /**
     * Set help text to display on the form view
     *
     * @param string $text Help text content
     * @return self
     */
    public function helpOnForm(string $text): self
    {
        $this->helpOnForm = $text;
        return $this;
    }

    /**
     * Set help text to display on the detail view
     *
     * @param string $text Help text content
     * @return self
     */
    public function helpOnDetail(string $text): self
    {
        $this->helpOnDetail = $text;
        return $this;
    }

    /**
     * Make the field readonly based on a callback condition
     *
     * @param callable $callback Receives (NovaRequest $request, $resource)
     * @return self
     */
    public function readonlyWhen(callable $callback): self
    {
        $this->readonlyWhenCallback = $callback;
        return $this;
    }

    /**
     * Set custom label text for ON and OFF states
     *
     * @param string|null $onLabel Text to display when ON (default: 'ON')
     * @param string|null $offLabel Text to display when OFF (default: 'OFF')
     * @return self
     */
    public function valueLabelText(?string $onLabel = 'ON', ?string $offLabel = 'OFF'): self
    {
        $this->onLabel = $onLabel;
        $this->offLabel = $offLabel;
        return $this;
    }

    /**
     * Set the label text color when toggle is ON
     *
     * @param string $light Color for light mode
     * @param string|null $dark Color for dark mode (defaults to light color)
     * @return self
     */
    public function valueLabelOnColors(string $light, ?string $dark = null): self
    {
        $this->onLabelColor = $light;
        $this->onLabelColorDark = $dark ?? $light;
        return $this;
    }

    /**
     * Set the label text color when toggle is OFF
     *
     * @param string $light Color for light mode
     * @param string|null $dark Color for dark mode (defaults to light color)
     * @return self
     */
    public function valueLabelOffColors(string $light, ?string $dark = null): self
    {
        $this->offLabelColor = $light;
        $this->offLabelColorDark = $dark ?? $light;
        return $this;
    }

    /**
     * Set which model attribute to use for toast notification label
     *
     * By default, uses fallback order: name → label → title → resource singular label
     *
     * @param string $key Model attribute name
     * @return self
     */
    public function toastLabelKey(string $key): self
    {
        $this->toastLabelKey = $key;
        return $this;
    }

    /**
     * Toggle the display of toast notifications
     *
     * @param bool $show Whether to show toast (default: true)
     * @return self
     */
    public function toastShow(bool $show = true): self
    {
        $this->toastShow = $show;
        return $this;
    }

    /**
     * Make the field filterable in the index view
     *
     * Uses Nova's Boolean filter functionality
     *
     * @return self
     */
    public function filterable(): self
    {
        $this->shouldBeFilterable = true;
        return $this;
    }

    /**
     * Serialize the field for filter usage
     *
     * Delegates to Boolean field's filter serialization when filterable
     *
     * @return mixed
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
     * Create a filter instance for this field
     *
     * @param NovaRequest $request
     * @return BooleanFilter|null
     */
    protected function makeFilter(NovaRequest $request)
    {
        if ($this->shouldBeFilterable) {
            return new BooleanFilter($this->attribute);
        }

        return null;
    }

    /**
     * Serialize the field for JSON response
     *
     * Includes all color settings, labels, callbacks, and computed states
     *
     * @return array
     */
    public function jsonSerialize(): array
    {
        $request = app(NovaRequest::class);
        $hidden = false;
        $readonly = $this->isReadonly($request);

        // Check if field should be hidden based on callback
        if ($this->hideWhenCallback && is_callable($this->hideWhenCallback)) {
            $hidden = call_user_func($this->hideWhenCallback, $request, $this->resource ?? null);
        }

        // Check if field should be readonly based on callback
        if (!$readonly && $this->readonlyWhenCallback && is_callable($this->readonlyWhenCallback)) {
            $readonly = call_user_func($this->readonlyWhenCallback, $request, $this->resource ?? null);
        }

        // Check authentication guards for readonly state
        if (!$readonly) {
            $guards = config('nova-toggle-5.guards', ['web']);
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
