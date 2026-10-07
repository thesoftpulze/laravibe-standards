---
name: laravibe-standards-development
description: >
  Use when creating, editing, or refactoring DTOs, Enums, API Resources, Action classes, or publishing stubs in a Laravel application installed with thesoftpulze/laravibe-standards.
license: MIT
metadata:
  author: Ashok Barua Akas
---

# Laravibe Standards

Use this skill when a Laravel application needs to integrate the Laravibe Standards package.

## Primary Goal

- apply the `thesoftpulze/laravibe-standards` package's public API in the smallest correct way

## Workflow

### 1. Inspect the Laravel app context

- confirm the app is a Laravel project
- inspect the target code paths where the package should be applied

### 2. Apply the DTO convention

The package provides immutable, typed DTOs with automatic hydration and serialization.

- generate a DTO: `php artisan make:dto {Name}`
- add typed `public readonly` constructor parameters for each field
- hydrate with `YourDTO::from($request)`, `YourDTO::fromArray($data)`, or `YourDTO::fromRequest($request)`
- serialize with `toArray()`, `toJson()`, `toEloquent()`, or `forEloquent($extra)`
- create an updated copy with `$dto->with(['field' => 'new value'])`
- extend the DTO with custom static constructors like `fromModel($model)` that delegate to `fromArray`

### 3. Apply the Enum convention

The package provides a shared `HasEnumMetadata` trait with label, option, and validation helpers for backed and unit enums.

- generate an enum (int-backed): `php artisan make:enum {Name} --int`
- after publishing stubs, generated enums include `use HasEnumMetadata`
- use `options()`, `values()`, `names()` for structured output
- use `isValidValue()`, `isValidName()`, `fromValueOrFail()` for validation

### 4. Apply the Resource convention

The package provides base resource classes for API and Inertia responses with reusable field helpers.

- generate a resource: `php artisan make:resource {Name}` (uses package stubs after publishing)
- generate a collection: `php artisan make:resource {Name}Collection --collection`
- extend `TheSoftPulze\LaravibeStandards\Resources\AppResource` for single resources
- extend `TheSoftPulze\LaravibeStandards\Resources\AppResourceCollection` for collections
- use helpers in `toArray()`: `id()`, `attribute()`, `optionalAttribute()`, `relation()`
- use `toInertia()` when passing resources to `inertia()` props

### 5. Publish stubs for customization (optional)

```bash
php artisan vendor:publish --tag="laravibe-standards-stubs"
```

This publishes DTO, enum, and resource stubs:
- `stubs/laravibe-standards/dto.stub` — used by `make:dto`
- `stubs/enum.stub` — used by `make:enum` for pure enums
- `stubs/enum.backed.stub` — used by `make:enum` for backed enums
- `stubs/resource.stub` — used by `make:resource` for single resources
- `stubs/resource-collection.stub` — used by `make:resource --collection`

## DTO Conventions

Follow these rules when creating DTOs with Laravibe Standards.

### Placement Rules

- Place DTOs only in `app/DTOs` or `app/DTOs/{Domain}`.
- Use domain folders for feature grouping, for example `app/DTOs/Billing` or `app/DTOs/Account`.
- Use `make:dto` command to generate files.

### Class Rules

- Every DTO must be a `final readonly` class.
- Every DTO must use `TheSoftPulze\LaravibeStandards\DTOs\Concerns\AsDTO`.
- Implement `Arrayable` and `Jsonable` when used as shared response payloads.

### Naming Rules

- Use singular names ending with `DTO`.
- Prefer purpose-based names such as `CreateInvoiceDTO`, `UpdateProfileDTO`, `ListFiltersDTO`.

### Property Typing Rules

- Constructor promotion is required.
- Use strict scalar and object types where possible.
- Keep nullable types explicit.
- Avoid `mixed` unless absolutely necessary.
- Required parameters must come before optional parameters.

### Hydration and Output Rules

- Hydrate DTOs with `::from()`, `::fromArray()`, or `::fromRequest()`.
- Unknown request keys are ignored by default.
- To enable strict unknown-key validation in a DTO, override `shouldThrowOnUnknownKeys(): bool` and return `true`.
- Use `toArray()` for general serialization and `toEloquent()` for model-friendly attributes.

### Design Rules

- Keep DTOs small and focused on transport only.
- Do not put database queries or heavy business logic inside DTOs.
- Use custom constructors like `fromModel()` as thin wrappers that delegate to `fromArray()`.

## Enum Conventions

Follow these rules when creating enums with Laravibe Standards.

### Placement Rules

- Place enums in `app/Enums`.
- Place shared enum concerns in `app/Enums/Concerns`.
- Use `make:enum` command to generate files.

### Backing Type Rules

- Prefer int-backed enums for values that are stored in the database or serialized in payloads. Generate with `--int`.
- Use string-backed enums only when the stored value must be human-readable or interoperates with an external system (e.g., API keys, standard status strings). Generate with `--string`.
- Use unit enums (no flag) only for in-memory domain concepts that never cross a storage boundary.

### Base Concern Contract

The shared concern `HasEnumMetadata` provides exactly these 8 core methods:

1. `label(): string`
2. `toOption(): array{name: string, value: int|string, label: string}`
3. `options(): array<int, array{name: string, value: int|string, label: string}>`
4. `values(): array<int, int|string>`
5. `names(): array<int, string>`
6. `isValidValue(int|string $value): bool`
7. `isValidName(string $name): bool`
8. `fromValueOrFail(int|string $value): self`

One optional method is also available:

1. `tryFromName(string $name): ?self`

### Design Rules

- Keep shared enum concerns pure and deterministic.
- Do not add database calls, HTTP calls, or heavy business logic inside enum concerns.
- Put domain-specific behavior on individual enum classes.
- Keep DTO enum serialization behavior in `AsDTO` unchanged.

## Resource Conventions

Follow these rules when creating resources with Laravibe Standards.

### Placement Rules

- Place resources only in `app/Http/Resources`.
- Use domain subfolders for feature grouping, for example `app/Http/Resources/Admin` or `app/Http/Resources/Account`.
- Use `make:resource` command to generate files.

### Class Rules

- Every resource must be a `final class`.
- Single resources must extend `\TheSoftPulze\LaravibeStandards\Resources\AppResource`.
- Collection resources must extend `\TheSoftPulze\LaravibeStandards\Resources\AppResourceCollection`.
- Implement `Illuminate\Http\Request` type-hinting in `toArray()`.

### Naming Rules

- Use singular names ending with `Resource` for single resources (e.g., `UserResource`, `PostResource`).
- Use singular or collection names ending with `Collection` for collection resources (e.g., `UserCollection`, `PostCollection`).

### Field Definition Rules

- Use the `FlexibleJsonResource` trait helpers exclusively in `toArray()`.
- Core helpers: `id()`, `attribute()`, `optionalAttribute()`, `relation()`.
- Timestamp helpers: `createdAt()`, `updatedAt()`, `deletedAt()`, `timestamps()`, `softDeleteTimestamps()`.
- Support field customization with `alias`, `prefix`, and `suffix` parameters.
- Never directly access model attributes or relations — use the trait helpers.

### Serialization Rules

- Use `toArray()` for API responses.
- Use `toInertia()` (from `AppResource` or `AppResourceCollection`) when passing to Vue components.
- Relations must use `relation()` to ensure they're only serialized if eager-loaded, preventing N+1 queries.

### Design Rules

- Keep resources focused on serialization only — no business logic, queries, or transformations.
- Prefer eager-loading via query builder over lazy loading via `relation()`.

## Rules, References, and Templates

Read before executing:

- refer to `src/DTOs/README.md` inside the package for the full DTO convention guide
- refer to `src/Enums/README.md` inside the package for the full enum guide
- refer to `src/Resources/README.md` inside the package for the full resource guide

## Examples

### Generate and use a DTO

```php
// 1. Generate the DTO
// php artisan make:dto Account/UpdateProfileDTO

// 2. Define the constructor parameters
final readonly class UpdateProfileDTO implements Arrayable, Jsonable
{
    use \TheSoftPulze\LaravibeStandards\DTOs\Concerns\AsDTO;

    public function __construct(
        public string $name,
        public ?string $bio = null,
    ) {
    }
}

// 3. Hydrate from a form request
$dto = UpdateProfileDTO::from($request);

// 4. Use in a controller action
public function __invoke(UpdateProfileRequest $request)
{
    $dto = UpdateProfileDTO::from($request);
    $this->service->update($dto);
    return redirect()->back();
}
```

### Prompt templates for agents

- Create `app/DTOs/Account/UpdateProfileDTO` as `final readonly` using `AsDTO` with `name`, `username`, and nullable `bio`.
- Generate a `Billing/CreateInvoiceDTO` with typed properties and a `fromModel` helper for draft invoices.
- Refactor existing DTOs in `app/DTOs` to use `AsDTO` and replace manual `toArray` and `toJson` methods.
- Create `app/Enums/ToastType` as an int-backed enum using `HasEnumMetadata` with cases like `Error`, `Success`, `Warning`.
- Generate a `Billing/InvoiceStatus` enum with `HasEnumMetadata` and a helper to check if the invoice can be paid.
- Refactor an existing plain enum in `app/Enums` to use `HasEnumMetadata` and replace manual helper methods.
- Create a `UserResource` that extends `AppResource` and includes `id()`, `attribute('name')`, `attribute('email')`, and `timestamps()`.
- Generate a `PostCollection` resource and refactor `PostResource` to use it with pagination.
- Add a `relation()` helper for posts inside `UserResource` to prevent N+1 queries.
- Create `app/Http/Resources/Admin/AdminUserResource` that extends `AppResource` with admin-specific fields.
- Update `UserResource` to use `optionalAttribute()` for nullable fields like `email_verified_at`.

## Anti-patterns

- do not add business logic inside DTOs; keep them focused on data transfer
- do not call `from()` with an unprepared request; validate first or rely on form request `validated()`
- do not use `mixed` for DTO properties when a strict type is available
- do not skip `readonly` — DTOs must be immutable
- do not document package internals here; keep the skill focused on adoption in Laravel apps
- do not add business logic or database queries inside resource `toArray()` methods
- do not access model relations directly in `toArray()`; use `relation()` instead
- do not skip `toInertia()` when passing resources to `inertia()` props
