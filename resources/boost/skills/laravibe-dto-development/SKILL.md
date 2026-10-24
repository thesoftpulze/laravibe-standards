---
name: laravibe-dto-development
description: Create, edit, or refactor Data Transfer Objects (DTOs) in app/DTOs using the AsDTO trait from thesoftpulze/laravibe-standards, including hydrating them from requests or arrays and serializing them for responses or Eloquent.
license: MIT
metadata:
  author: Ashok Barua Akas
---

# Laravibe DTO Development

## When to use this skill

Use this skill when creating a DTO, moving request or array data into a typed object, passing data between controllers, actions, and services, or writing DTO data to Eloquent models.

## Create a DTO

```bash
php artisan make:dto Account/UpdateProfileDTO
```

This creates `app/DTOs/Account/UpdateProfileDTO.php`. The generated class has placeholder `name` and `description` parameters: replace them with the real fields.

```php
<?php

declare(strict_types=1);

namespace App\DTOs\Account;

use App\Enums\AccountStatus;
use Carbon\CarbonImmutable;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\Support\Jsonable;
use TheSoftPulze\LaravibeStandards\DTOs\Concerns\AsDTO;

/**
 * @implements Arrayable<string, mixed>
 */
final readonly class UpdateProfileDTO implements Arrayable, Jsonable
{
    use AsDTO;

    public function __construct(
        public string $name,
        public AccountStatus $status,
        public ?string $bio = null,
        public ?CarbonImmutable $birthday = null,
    ) {}
}
```

## Rules

- Place DTOs in `app/DTOs` or `app/DTOs/{Domain}`. Name them after their purpose and end with `DTO`, for example `CreateInvoiceDTO`.
- Make every DTO `final readonly`, use `AsDTO`, and declare every field as a promoted constructor parameter.
- Type every parameter. Avoid `mixed` and untyped parameters: they skip casting and validation.
- Put required parameters before optional ones.
- Keep DTOs to data transfer only. No queries, HTTP calls, or business logic.

## Hydrate

- `UpdateProfileDTO::from($request)` or `UpdateProfileDTO::fromRequest($request)`: uses `$request->validated()` when the request is a `FormRequest`, and `$request->all()` otherwise. Pass a `FormRequest` so only validated data reaches the DTO.
- `UpdateProfileDTO::from($array)` or `UpdateProfileDTO::fromArray($array)`.
- Keys are matched to constructor parameter names exactly, so input keys must use the same names (`birthday`, not `birth_day`).
- Unknown keys are ignored. To reject them, add this to the DTO:

  ```php
  protected static function shouldThrowOnUnknownKeys(): bool
  {
      return true;
  }
  ```

Values are cast to the parameter type:

| Parameter type | Accepted input |
| --- | --- |
| `int`, `float` | numbers and numeric strings |
| `bool` | booleans and `"1"`, `"0"`, `"true"`, `"false"`, `"on"`, `"off"`, `"yes"`, `"no"` |
| `string` | strings, numbers, booleans |
| backed enum | its backing value |
| unit enum | the case name |
| `DateTimeInterface`, `Carbon`, `CarbonImmutable` | date strings and timestamps |
| another DTO (any class with `fromArray()`) | a nested array |

A missing key falls back to the parameter default, then to `null` when the type is nullable. Otherwise, and for any value that can't be cast, an `InvalidArgumentException` is thrown with the class and property name.

`array` parameters are not cast item by item. For a list of nested DTOs, map it in a named constructor.

## Named constructors

Add named constructors as thin wrappers that delegate to `fromArray()`:

```php
public static function fromModel(User $user): self
{
    return self::fromArray([
        'name' => $user->name,
        'status' => $user->status,
        'bio' => $user->bio,
    ]);
}
```

## Serialize

- `toArray()`: enums become their value (backed) or name (unit), dates become ISO 8601 strings, and nested DTOs become arrays.
- `toJson()`: JSON of `toArray()`.
- `toEloquent()`: like `toArray()`, but dates use `Y-m-d H:i:s`. Use it for `Model::create()` and `$model->update()`.
- `forEloquent(['user_id' => $user->id])`: `toEloquent()` merged with extra attributes.
- `$dto->with(['bio' => 'New bio'])`: returns a new DTO with the changed values, hydrated and validated again.

```php
public function update(UpdateProfileRequest $request, Profile $profile): RedirectResponse
{
    $dto = UpdateProfileDTO::from($request);

    $profile->update($dto->toEloquent());

    return back();
}
```

## Anti-patterns

- Hydrating from `$request->all()` when a `FormRequest` with validation rules exists.
- Mutable DTOs, or classes without `readonly`.
- Hand-written `toArray()`, `toJson()`, or `fromArray()` methods in a DTO that uses `AsDTO`.
- Queries, services, or side effects inside a DTO.
