# Laravel API Response Package

A fluent, extensible, and developer-friendly API response builder for Laravel applications.

---

## 🚀 Features

- ✨ **Fluent Interface** — Chain methods to build responses easily.
- 🧩 **Response Types** — Supports `success`, `error`, `validation`, `not found`, and more.
- ⚙️ **Customizable Messages** — Override defaults or localize them.
- 🧱 **Macroable Support** — Extend response types dynamically.
- 🧮 **Meta Data Support** — Add pagination or extra info with `meta()`.
- 🌍 **Localization Ready** — Pull response messages from Laravel translations.
- 🔍 **Trace Support** — Optionally include exception traces for debugging.

---

## 📦 Installation

```bash
composer require bhry98/laravel-api-response
```

---

## ⚙️ Configuration

Publish the configuration file:

```bash
php artisan vendor:publish --provider="Bhry98\LaravelApiResponse\Providers\LaravelApiResponseServiceProvider" --tag=config
```

This will create `config/bhry98-api-response.php` where you can customize default messages:

```php
return [
    'messages' => [
        'success' => 'Operation successful',
        'error' => 'An error occurred',
        'validation' => 'Validation failed',
        'not_found' => 'Resource not found',
        'internal' => 'Internal server error',
    ],
];
```

---

## 🧠 Usage Example

```php
use Bhry98\LaravelApiResponse\Facades\BResponseSuccess;
use Bhry98\LaravelApiResponse\Facades\BResponseError;

Route::get('/{t}', function ($t) {
    return match ($t) {
        "success" => BResponseSuccess::make()
                ->message('Everything is fine!')
                ->data(['id' => 1])
                ->additions(['request_id' => uniqid()])
                ->toJson(),
        default => BResponseError::make()
                ->message('Something went wrong!')
                ->trace(new \Exception('Example'))
                ->toJson(),
    };
});
```

---

## 🧩 Example Response

### ✅ Success
```json
{
  "status": "success",
  "message": "Everything is fine!",
  "data": { "id": 1 },
  "additions": { "request_id": "654adf..." },
  "meta": []
}
```

### ❌ Error
```json
{
  "status": "error",
  "message": "Something went wrong!",
  "trace": "Exception stack trace (optional)",
  "data": [],
  "additions": [],
  "meta": []
}
```

---

## 🧱 Extend Response (Macroable)

```php
BResponseSuccess::macro('withTimestamp', function () {
    return $this->additions(['timestamp' => now()]);
});

// Usage
return BResponseSuccess::make()
    ->withTimestamp()
    ->toJson();
```

---

## 🧩 Available Methods

| Method                     | Description                           |
|----------------------------|---------------------------------------|
| `make($options = [])`      | Initialize response                   |
| `message($message)`        | Set response message                  |
| `data($data)`              | Attach data payload                   |
| `additions(array $extras)` | Add custom key/value pairs            |
| `meta($info)`              | Add pagination/meta info              |
| `trace($exception)`        | Include exception trace               |
| `localize($key)`           | Use translation key for message       |
| `toJson()`                 | Return `Illuminate\Http\JsonResponse` |

---

## ⚖️ License

This package is open-sourced software licensed under the [MIT license](https://opensource.org/license/MIT).

---

Developed with ❤️ by **Bhry98**
