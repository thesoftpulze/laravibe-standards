<div align="center">
    <h1>Laravibe Standards</h1>
</div>

<p align="center">
    <a href="https://packagist.org/packages/thesoftpulze/laravibe-standards"><img src="https://img.shields.io/packagist/v/thesoftpulze/laravibe-standards.svg?style=flat-square" alt="Packagist"></a>
    <a href="https://packagist.org/packages/thesoftpulze/laravibe-standards"><img src="https://img.shields.io/packagist/php-v/thesoftpulze/laravibe-standards.svg?style=flat-square" alt="PHP from Packagist"></a>
    <a href="https://packagist.org/packages/thesoftpulze/laravibe-standards"><img src="https://badge.laravel.cloud/badge/thesoftpulze/laravibe-standards?style=flat" alt="Laravel versions"></a>
    <a href="https://github.com/thesoftpulze/laravibe-standards/actions"><img alt="GitHub Workflow Status (main)" src="https://img.shields.io/github/actions/workflow/status/thesoftpulze/laravibe-standards/tests.yml?branch=main&label=Tests&style=flat-square"></a>
    <a href="https://packagist.org/packages/thesoftpulze/laravibe-standards"><img src="https://img.shields.io/packagist/dt/thesoftpulze/laravibe-standards.svg?style=flat-square" alt="Total Downloads"></a>
</p>

Standard conventions, structure, and tooling for Laravel apps built the LaraVibe way.

## Installation

You can install the package via Composer:

```bash
composer require thesoftpulze/laravibe-standards
```

### Upgrading from `softpulze/laravibe-standards`

This package was previously published as `softpulze/laravibe-standards` and used the `SoftPulze\LaravibeStandards` namespace. The old package is abandoned, and both the package name and namespace changed in `v0.4.0`.

1. Replace the requirement in your `composer.json`:

   ```bash
   composer remove softpulze/laravibe-standards
   composer require thesoftpulze/laravibe-standards
   ```

2. Update namespace imports and references from `SoftPulze\LaravibeStandards\...` to `TheSoftPulze\LaravibeStandards\...`.

You may publish all of the package's resources at once:

```bash
php artisan vendor:publish --tag="laravibe-standards"
```

Or, you may publish each resource individually:

### Publishing the Configuration File

```bash
php artisan vendor:publish --tag="laravibe-standards-config"
```

### Publishing Stubs

```bash
php artisan vendor:publish --tag="laravibe-standards-stubs"
```

## Usage

### DTOs (Data Transfer Objects)

Laravibe Standards provides a convention for defining immutable, typed DTOs with automatic hydration and serialization.

**Generate a DTO:**

```bash
php artisan make:dto UserProfileDTO
php artisan make:dto Account/UpdateProfileDTO
```

**Define a DTO:**

```php
final readonly class UserProfileDTO implements Arrayable, Jsonable
{
    use \TheSoftPulze\LaravibeStandards\DTOs\Concerns\AsDTO;

    public function __construct(
        public string $name,
        public ?string $bio = null,
    ) {
    }
}
```

**Hydrate and serialize:**

```php
$dto = UserProfileDTO::from($request);
$dto = UserProfileDTO::fromArray(['name' => 'Alice', 'bio' => 'Developer']);

$array = $dto->toArray();
$json  = $dto->toJson();
$data  = $dto->toEloquent();

$updated = $dto->with(['bio' => 'Senior Dev']);
```

See the [full DTO guide](src/DTOs/README.md) for conventions, type casting, and advanced usage.

### Enums

Laravibe Standards provides a shared `HasEnumMetadata` trait with label, option, and validation helpers for backed and unit enums.

**Generate an enum:**

```bash
php artisan make:enum ToastType --int
php artisan make:enum ToastType --string
```

**Define an enum:**

```php
enum ToastType: int
{
    use \TheSoftPulze\LaravibeStandards\Enums\Concerns\HasEnumMetadata;

    case Error = 1;
    case Success = 2;
    case Warning = 3;
    case Info = 4;
}
```

**Use helpers:**

```php
ToastType::options();    // [{name: 'Error', value: 1, label: 'Error'}, ...]
ToastType::values();     // [1, 2, 3, 4]
ToastType::isValidValue(2);  // true
ToastType::fromValueOrFail(1); // ToastType::Error
ToastType::Error->label();     // 'Error'
```

See the [full enum guide](src/Enums/README.md) for conventions, the concern contract, and design rules.

### Resources

Laravibe Standards provides base resource classes for API and Inertia responses with reusable field helpers.

**Generate a resource:**

```bash
php artisan make:resource UserResource
php artisan make:resource UserCollection --collection
```

**Define a resource:**

```php
final class UserResource extends \TheSoftPulze\LaravibeStandards\Resources\AppResource
{
    public function toArray(Request $request): array
    {
        return [
            $this->id(),
            $this->attribute('name'),
            $this->attribute('email'),
            ...$this->timestamps(),
        ];
    }
}
```

**Use in controllers:**

```php
UserResource::make($user);                           // single
UserResource::collection(User::paginate());          // paginated

// Inertia
return inertia('users/Show', ['user' => UserResource::make($user)->toInertia()]);
```

See the [full resource guide](src/Resources/README.md) for helpers, conventions, and serialization rules.

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Thank you for considering contributing to Laravibe Standards! Please review our [contributing guide](.github/CONTRIBUTING.md) to get started.

## Security Vulnerabilities

Please review [our security policy](.github/SECURITY.md) on how to report security vulnerabilities.

## Credits

- [Ashok Barua Akas](https://github.com/thesoftpulze)
- [All Contributors](../../contributors)

## License

Laravibe Standards is open-sourced software licensed under the [MIT license](LICENSE.md).
