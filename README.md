<div align="center">

# Laravibe Standards

**Conventions, generators and base classes for Laravel apps built the LaraVibe way.**

[![Packagist](https://img.shields.io/packagist/v/thesoftpulze/laravibe-standards.svg?style=flat-square)](https://packagist.org/packages/thesoftpulze/laravibe-standards)
[![PHP](https://img.shields.io/badge/PHP-8.4-777BB4?style=flat-square&logo=php)](https://php.net)
[![Laravel](https://img.shields.io/badge/Laravel-13-FF2D20?style=flat-square&logo=laravel)](https://laravel.com)
[![Tests](https://img.shields.io/github/actions/workflow/status/thesoftpulze/laravibe-standards/tests.yml?branch=main&label=Tests&style=flat-square)](https://github.com/thesoftpulze/laravibe-standards/actions)
[![Downloads](https://img.shields.io/packagist/dt/thesoftpulze/laravibe-standards.svg?style=flat-square)](https://packagist.org/packages/thesoftpulze/laravibe-standards)
[![License](https://img.shields.io/badge/License-MIT-brightgreen?style=flat-square)](LICENSE.md)

Made by [The SoftPulze](https://softpulze.com)

</div>

## Features

- **Actions:** `make:action` generates single-purpose action classes in `app/Actions`, with an optional test.
- **DTOs:** `make:dto` and the `AsDTO` trait for immutable, typed DTOs. See the [DTO guide](src/DTOs/README.md).
- **Enums:** the `HasEnumMetadata` trait adds labels, options and validation helpers. See the [enum guide](src/Enums/README.md).
- **Resources:** `AppResource` and `AppResourceCollection` with field helpers for API and Inertia responses. See the [resource guide](src/Resources/README.md).
- **Stubs** for `make:action`, `make:dto`, `make:enum` and `make:resource`.
- **Laravel Boost** guideline and skills for AI agents.

## Installation

Requires PHP 8.4+ and Laravel 13+.

```bash
composer require thesoftpulze/laravibe-standards
php artisan vendor:publish --tag="laravibe-standards-stubs"
```

The stubs are published to the application's `stubs` directory. Without them, `make:enum` and `make:resource` generate plain Laravel classes.

Upgrading from `softpulze/laravibe-standards`? Require `thesoftpulze/laravibe-standards` instead and rename `SoftPulze\LaravibeStandards` imports to `TheSoftPulze\LaravibeStandards`.

## Usage

### Actions

```bash
php artisan make:action Account/UpdateProfileAction --pest
```

```php
final readonly class UpdateProfileAction
{
    public function handle(User $user, UpdateProfileDTO $data): User
    {
        $user->update($data->toEloquent());

        return $user;
    }
}
```

Pass `--test`, `--pest` or `--phpunit` to also generate a test in `tests/Feature/Actions`.

### DTOs

```bash
php artisan make:dto Account/UpdateProfileDTO
```

```php
final readonly class UpdateProfileDTO implements Arrayable, Jsonable
{
    use \TheSoftPulze\LaravibeStandards\DTOs\Concerns\AsDTO;

    public function __construct(
        public string $name,
        public ?string $bio = null,
    ) {}
}

$dto = UpdateProfileDTO::from($request);
$dto->toArray();
$dto->toEloquent();
$dto->with(['bio' => 'Senior Dev']);
```

### Enums

```bash
php artisan make:enum ToastType --string
```

```php
enum ToastType: string
{
    use \TheSoftPulze\LaravibeStandards\Enums\Concerns\HasEnumMetadata;

    case Success = 'success';
    case Error = 'error';
}

ToastType::options();
ToastType::values();
ToastType::Success->label();
```

### Resources

```bash
php artisan make:resource UserResource
```

```php
final class UserResource extends \TheSoftPulze\LaravibeStandards\Resources\AppResource
{
    public function toArray(Request $request): array
    {
        return [
            $this->id(),
            $this->attribute('name'),
            ...$this->timestamps(),
        ];
    }
}

return inertia('users/Show', ['user' => UserResource::make($user)->toInertia()]);
```

## AI Agents

```bash
php artisan boost:install
```

When prompted for third-party packages, select **thesoftpulze/laravibe-standards** to install the action, DTO, enum and resource guidelines. Run `php artisan boost:update` after updating the package.

## Changelog

See [CHANGELOG](CHANGELOG.md) for recent changes.

## Contributing

See the [contributing guide](.github/CONTRIBUTING.md) and [security policy](.github/SECURITY.md).

## Credits

Laravibe Standards is developed and maintained by [The SoftPulze](https://softpulze.com).

- [Ashok Barua](https://ashokbaruaakas.com), creator and maintainer
- [All contributors](https://github.com/thesoftpulze/laravibe-standards/graphs/contributors)

Built on [Laravel](https://laravel.com).

## License

Laravibe Standards is open-source software licensed under the [MIT license](LICENSE.md). Copyright © 2025-present The SoftPulze.
