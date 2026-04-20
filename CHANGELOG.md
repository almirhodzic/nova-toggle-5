# Changelog

## v1.3.0 - 2026-04-20

### Security

- Fixed an authorization vulnerability in the toggle endpoint. Previously the route
  was protected only by `web` + `auth:<guard>` middleware, which let any
  authenticated user on the configured guard flip booleans — including users that
  do not have access to Nova (e.g. frontend customers sharing the `web` guard).
  The endpoint now uses Nova's `nova:api` middleware (enforcing the `viewNova`
  gate), and additionally checks the resource's `authorizedToUpdate` policy and
  restricts writes to attributes that are declared as a `Toggle` field on the
  resource and are not readonly in the current request context.

  Thanks to [Roberto Negro](https://github.com/RobertoNegro) for the responsible
  disclosure.

### Changed

- `ToggleServiceProvider` now registers routes behind `nova:api` instead of
  `['web', 'auth:<guards>']`.
- `ToggleController` now uses `NovaRequest` and evaluates authorization through
  the Nova resource.

### Removed

- **BREAKING**: The `config/nova-toggle-5.php` config file and its `guards`
  option have been removed. Authorization is now fully delegated to Nova's own
  access rules (`viewNova` gate + resource policies). If you previously published
  the config file, you can safely delete it.

## v1.2.1 - 2025-02-23

### Changed

- Moved `laravel/nova` from `require` to `require-dev`
  This prevents dependency conflicts and aligns with best practices for Nova field packages.
