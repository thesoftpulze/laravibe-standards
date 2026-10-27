---
name: laravibe-enum-development
description: Create, edit, or refactor PHP enums in app/Enums using the HasEnumMetadata trait from thesoftpulze/laravibe-standards, including labels, select options, value lists, and enum validation.
license: MIT
metadata:
  author: Ashok Barua
---

# Laravibe Enum Development

## When to use this skill

Use this skill when creating an enum, building select options or label lists from an enum, validating enum input, or replacing hand-written enum helper methods.

## Create an enum

```bash
php artisan make:enum InvoiceStatus --int     # int-backed
php artisan make:enum Currency --string       # string-backed
php artisan make:enum Direction               # unit enum
```

`make:enum` is Laravel's own command. It only adds `HasEnumMetadata` once the package stubs are published. If `stubs/enum.backed.stub` doesn't exist in the application, run this first, or add the trait by hand:

```bash
php artisan vendor:publish --tag=laravibe-standards-stubs
```

```php
<?php

declare(strict_types=1);

namespace App\Enums;

use TheSoftPulze\LaravibeStandards\Enums\Concerns\HasEnumMetadata;

enum InvoiceStatus: int
{
    use HasEnumMetadata;

    case Draft = 1;
    case InProgress = 2;
    case Paid = 3;

    public function isPayable(): bool
    {
        return $this === self::InProgress;
    }
}
```

## Rules

- Place enums in `app/Enums`, give them singular names, and use TitleCase case names.
- Use int-backed enums for values stored in the database or sent in payloads.
- Use string-backed enums when the stored value must be readable or must match an external system.
- Use unit enums only for values that never leave memory.
- Use `HasEnumMetadata` on every enum. Put domain behavior, such as `isPayable()`, on the enum itself.

## Helpers

| Method                                  | Returns                                                                                                    |
| --------------------------------------- | ---------------------------------------------------------------------------------------------------------- |
| `InvoiceStatus::InProgress->label()`    | `"In Progress"`, derived from the case name                                                                |
| `InvoiceStatus::InProgress->toOption()` | `['name' => 'InProgress', 'value' => 2, 'label' => 'In Progress']`                                         |
| `InvoiceStatus::options()`              | `toOption()` for every case, for select inputs                                                             |
| `InvoiceStatus::values()`               | backing values (case names for unit enums)                                                                 |
| `InvoiceStatus::names()`                | case names                                                                                                 |
| `InvoiceStatus::isValidValue(2)`        | `true`; the comparison is strict, so `'2'` is `false`                                                      |
| `InvoiceStatus::isValidName('paid')`    | `true`; case-insensitive                                                                                   |
| `InvoiceStatus::tryFromName('paid')`    | `InvoiceStatus::Paid` or `null`; case-insensitive                                                          |
| `InvoiceStatus::fromValueOrFail(3)`     | `InvoiceStatus::Paid`; falls back to a case-insensitive name match, then throws `InvalidArgumentException` |

To change a label, override `label()` on the enum:

```php
public function label(): string
{
    return match ($this) {
        self::InProgress => __('Awaiting payment'),
        default => str($this->name)->headline()->toString(),
    };
}
```

## Validation and casting

- Validate request input with Laravel's enum rule: `'status' => ['required', Rule::enum(InvoiceStatus::class)]`.
- Cast model attributes with `protected function casts(): array { return ['status' => InvoiceStatus::class]; }`.
- Request input is a string. Cast it before calling `isValidValue()` on an int-backed enum: `InvoiceStatus::isValidValue((int) $value)`.
- DTOs that use `AsDTO` hydrate enum parameters from backing values or case names automatically.

## Anti-patterns

- Hand-written `options()`, `values()`, or `label()` helpers that duplicate the trait.
- Queries, HTTP calls, or service container calls inside an enum.
- Storing case names in the database for a backed enum. Store the backing value.
