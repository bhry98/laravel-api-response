# Laravel API Response

[![Latest Version on Packagist](https://img.shields.io/packagist/v/bhry98/laravel-api-response.svg?style=flat-square)](https://packagist.org/packages/bhry98/laravel-api-response)
[![Total Downloads](https://img.shields.io/packagist/dt/bhry98/laravel-api-response.svg?style=flat-square)](https://packagist.org/packages/bhry98/laravel-api-response)
[![License](https://img.shields.io/packagist/l/bhry98/laravel-api-response.svg?style=flat-square)](https://packagist.org/packages/bhry98/laravel-api-response)

A fluent, extensible, highly configurable API response toolkit for Laravel 11, 12 and 13. Zero configuration to get started, endlessly customizable when you need it.

```php
return api()->created($user);

return api()
    ->success()
    ->message('User created.')
    ->data($user)
    ->meta(['foo' => 'bar'])
    ->headers(['X-Foo' => 'bar'])
    ->send();
```

## 📖 Full documentation

**[bhry98.github.io/packages/laravel-api-response](https://bhry98.github.io/packages/laravel-api-response/)**

## Installation

```bash
composer require bhry98/laravel-api-response
```

The service provider and the `ApiResponse` facade alias are auto-discovered - nothing else is required.

## Highlights

- A single fluent builder (`api()` / `ApiResponse`) with ready-made HTTP and business response shortcuts.
- Fully configurable envelope - renameable keys, optional wrapping, and swappable `default`/`minimal`/`mobile`/`jsonapi` presets.
- Automatic data detection for models, collections, resources, paginators and DTOs.
- Global metadata, hooks, events, macros and a template system for reusable responses.
- Opt-in **rate limiting** middleware with a standard `api()->tooManyRequests()` envelope.
- Opt-in **audit logging** of requests/responses to the database via a queued job, so logging never slows down a response.
- Opt-in **locale resolution** middleware (`Accept-Language` header first, then a configurable user locale column).
- Optional exception handler and `response()->json()` overrides, and testing assertion macros.
- Fully backward compatible with the pre-2.0 `BResponseSuccess`/`BResponseError`/... API.

See the [full documentation](https://bhry98.github.io/packages/laravel-api-response/) for configuration, usage examples and every extension point.

## License

MIT.
