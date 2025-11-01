# Nova Toggle

A Laravel Nova 5 toggle field that allows quick boolean updates directly from the index view.

![License](https://img.shields.io/badge/license-MIT-blue.svg)
![Nova](https://img.shields.io/badge/Nova-5.x-blue.svg)
![PHP](https://img.shields.io/badge/PHP-8.2+-blue.svg)

## Features

- 🎯 Quick toggle directly from index view
- 🎨 Customizable colors for light and dark mode
- 🔒 Built-in readonly and visibility controls
- 💡 Optional help text for different views
- 🔄 Optional page reload after toggle
- 🏷️ Custom ON/OFF labels with color customization
- ⚡ Vue 3 Composition API
- 🌓 Full dark mode support

## ⚠️ Beta Notice

**This package is currently in beta phase.**

Please note that this software is provided "as is", without warranty of any kind, express or implied. By installing and using this package, you acknowledge that:

- The package is still under active development and may contain bugs
- Features and APIs may change in future releases
- You use this package at your own risk
- The author(s) shall not be held liable for any damages, data loss, or issues arising from the use of this package
- It is recommended to thoroughly test the package in a development environment before using it in production

We appreciate your feedback and bug reports to help improve this package!

### 🐛 Found a Bug or Issue?

I would greatly appreciate if you could report any bugs, irregularities, or unexpected behavior you encounter. Your feedback helps make this package better for everyone!

Please report issues here: [GitHub Issues](https://github.com/almirhodzic/nova-toggle/issues)

**Thank you for your help!** 🙏

## Installation

```bash
composer require almirhodzic/nova-toggle
```

The service provider will be automatically registered.

## Basic Usage

```php
use AlmirHodzic\NovaToggle\Toggle;

public function fields(NovaRequest $request)
{
    return [
        Toggle::make('Active', 'is_active'),
    ];
}
```

## Configuration

### Colors

#### Toggle Background Colors

```php
Toggle::make('Active', 'is_active')
    ->onColor('#00d5be', '#009689')  // Light mode, Dark mode
    ->offColor('#e5e5e5', '#323f57');
```

#### Bullet Colors

```php
Toggle::make('Active', 'is_active')
    ->onBullet('#ffffff')              // Same for both modes
    ->offBullet('#ffffff', '#cccccc'); // Light mode, Dark mode
```

### Labels

#### Custom ON/OFF Text

```php
Toggle::make('Active', 'is_active')
    ->valueLabelText('ON', 'OFF');  // ON label, OFF label
```

#### Label Colors

```php
Toggle::make('Active', 'is_active')
    ->valueLabelText('ON', 'OFF')
    ->valueLabelOnColors('#ffffff')              // ON label color
    ->valueLabelOffColors('#a1a1a1', '#737373'); // OFF label colors (light, dark)
```

### Help Text

Add contextual help text for different views:

```php
Toggle::make('Active', 'is_active')
    ->helpOnIndex('Toggle to activate/deactivate')
    ->helpOnForm('Enable this option to activate the feature')
    ->helpOnDetail('Current activation status');
```

### Behavior

#### Reload Page After Toggle

```php
Toggle::make('Active', 'is_active')
    ->reloadPage(true);
```

### Visibility & Access Control

#### Hide Based on Condition

```php
Toggle::make('Active', 'is_active')
    ->hideWhen(function ($request, $resource) {
        return $resource->status === 'archived';
    });
```

#### Readonly Based on Condition

```php
Toggle::make('Active', 'is_active')
    ->readonlyWhen(function ($request, $resource) {
        return !$request->user()->isAdmin();
    });
```

#### Guard-Based Access Control

By default, the toggle checks authentication guards defined in your config. Create a config file:

```php
// config/nova-toggle.php
return [
    'guards' => ['web', 'admin'],
];
```

## Complete Example

```php
use AlmirHodzic\NovaToggle\Toggle;

public function fields(NovaRequest $request)
{
    return [
        ID::make()->sortable(),

        Text::make('Name'),

        Toggle::make('Active', 'is_active')
            ->onColor('#10b981', '#059669')
            ->offColor('#ef4444', '#dc2626')
            ->onBullet('#ffffff')
            ->offBullet('#ffffff')
            ->valueLabelText('ACTIVE', 'INACTIVE')
            ->valueLabelOnColors('#ffffff')
            ->valueLabelOffColors('#fecaca', '#fca5a5')
            ->helpOnIndex('Click to toggle status')
            ->helpOnForm('Enable to make this item visible')
            ->reloadPage(false)
            ->readonlyWhen(function ($request, $resource) {
                return !$request->user()->can('edit', $resource);
            }),

        Toggle::make('Featured', 'is_featured')
            ->onColor('#f59e0b')
            ->offColor('#6b7280')
            ->valueLabelText('★', '☆')
            ->hideWhen(function ($request, $resource) {
                return !$resource->is_active;
            }),
    ];
}
```

## API Reference

### Methods

| Method                  | Parameters                                           | Description                      |
| ----------------------- | ---------------------------------------------------- | -------------------------------- |
| `onColor()`             | `string $light, ?string $dark = null`                | Background color when ON         |
| `offColor()`            | `string $light, ?string $dark = null`                | Background color when OFF        |
| `onBullet()`            | `string $light, ?string $dark = null`                | Bullet color when ON             |
| `offBullet()`           | `string $light, ?string $dark = null`                | Bullet color when OFF            |
| `valueLabelText()`      | `?string $onLabel = 'ON', ?string $offLabel = 'OFF'` | Custom label text                |
| `valueLabelOnColors()`  | `string $light, ?string $dark = null`                | ON label color                   |
| `valueLabelOffColors()` | `string $light, ?string $dark = null`                | OFF label color                  |
| `reloadPage()`          | `bool $reload = true`                                | Reload page after toggle         |
| `hideWhen()`            | `callable $callback`                                 | Hide field based on condition    |
| `readonlyWhen()`        | `callable $callback`                                 | Make readonly based on condition |
| `helpOnIndex()`         | `string $text`                                       | Help text on index view          |
| `helpOnForm()`          | `string $text`                                       | Help text on form view           |
| `helpOnDetail()`        | `string $text`                                       | Help text on detail view         |

### Default Colors

| State          | Light Mode | Dark Mode |
| -------------- | ---------- | --------- |
| ON Background  | `#00d5be`  | `#009689` |
| OFF Background | `#e5e5e5`  | `#323f57` |
| ON Bullet      | `#ffffff`  | `#ffffff` |
| OFF Bullet     | `#ffffff`  | `#ffffff` |
| ON Label       | `#ffffff`  | `#ffffff` |
| OFF Label      | `#a1a1a1`  | `#737373` |

## Requirements

- PHP 8.2+
- Laravel Nova 5.x
- Laravel 10.x, 11.x, or 12.x

## Support

- [Issues](https://github.com/almirhodzic/nova-toggle/issues)
- [Source Code](https://github.com/almirhodzic/nova-toggle)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.

## Credits

- [Almir Hodzic](https://frontbyte.ch)
- [All Contributors](../../contributors)

---

Made with ❤️ by [Frontbyte](https://frontbyte.ch)
