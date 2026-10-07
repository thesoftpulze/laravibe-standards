# Enum Guide

## Purpose

Enums define finite domain values and expose a shared, predictable helper contract for labels, validation, and option payloads.

## Location and Naming

- Place enums in `app/Enums`.
- Place shared enum concerns in `app/Enums/Concerns`.
- Use singular, purpose-based enum names, for example `ToastType`.
- Use TitleCase case names, for example `Error`, `Success`, `Warning`.

## Backing Type

- Prefer int-backed enums for values that are stored in the database or serialized in payloads. Generate with `--int`.
- Use string-backed enums only when the stored value must be human-readable or interoperates with an external system (e.g., API keys, standard status strings). Generate with `--string`.
- Use unit enums (no flag) only for in-memory domain concepts that never cross a storage boundary.

## Shared Concern

Use `TheSoftPulze\LaravibeStandards\Enums\Concerns\HasEnumMetadata` as the base concern for common enum behavior.

### Core Contract (Required)

The base concern exposes these 8 methods:

1. `label(): string`
2. `toOption(): array{name: string, value: int|string, label: string}`
3. `options(): array<int, array{name: string, value: int|string, label: string}>`
4. `values(): array<int, int|string>`
5. `names(): array<int, string>`
6. `isValidValue(int|string $value): bool`
7. `isValidName(string $name): bool`
8. `fromValueOrFail(int|string $value): self`

### Optional Method

Only one optional method is part of the shared base concern:

1. `tryFromName(string $name): ?self`

## Usage Example

```php
<?php

declare(strict_types=1);

namespace App\Enums;

use TheSoftPulze\LaravibeStandards\Enums\Concerns\HasEnumMetadata;

enum ToastType: int
{
    use HasEnumMetadata;

    case Error = 1;
    case Success = 2;
    case Warning = 3;
    case Info = 4;
}
```

```php
ToastType::options();
ToastType::values();
ToastType::isValidValue(2);
ToastType::tryFromName('Warning');
ToastType::fromValueOrFail(1);
```

## Design Rules

- Keep shared concerns pure and deterministic.
- Do not add database queries, HTTP concerns, or service container calls to enum concerns.
- Keep domain-specific behavior on the enum itself instead of the shared concern.
- Keep DTO enum serialization unchanged; `AsDTO` already normalizes enum values for payloads.
