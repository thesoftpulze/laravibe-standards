# HTTP Resources

This package provides a base resource layer for API responses and Inertia props, keeping resource classes small and predictable.

## What's provided

- **`AppResource`** — base class for single resources. Extends `JsonResource` and includes the `FlexibleJsonResource` trait helpers.
- **`AppResourceCollection`** — base class for resource collections. Adds `toInertia()` for paginated and plain collections.
- **`FlexibleJsonResource`** — shared trait with field helpers used by `AppResource`.

## Setup

Publish stubs to override Laravel's built-in `make:resource` templates:

```bash
php artisan vendor:publish --tag="laravibe-standards-stubs"
```

After publishing, generated resources use the package base classes automatically.

## Creating a resource

```bash
php artisan make:resource UserResource
```

This generates a class extending `TheSoftPulze\LaravibeStandards\Resources\AppResource`:

```php
final class UserResource extends AppResource
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

### Naming rules

- Single resources: singular name ending with `Resource` (e.g. `UserResource`, `PostResource`)
- Collections: singular or collection name ending with `Collection` (e.g. `UserCollection`, `PostCollection`)
- Domain grouping: use subfolders like `Admin/AdminUserResource`

## Field helpers

All helpers return `MergeValue|MissingValue` so undefined fields are omitted from the response.

### `id()`
Returns the resource's `id` attribute.

### `attribute(string $key, bool $optional = false, ?Closure $resolver = null, ?string $alias = null, string $prefix = '', string $suffix = '')`
Returns a field with the given key. Supports aliasing, prefix/suffix, and value transformation:

```php
$this->attribute('name', alias: 'display_name')
$this->attribute('status', resolver: fn (Status $s): string => $s->value)
```

### `optionalAttribute(string $key, ...)`
Same as `attribute()` but only includes the field if it exists on the underlying model.

### `relation(string $key, ?Closure $resolver = null, ...)`
Only serializes a relation if it was eager-loaded, preventing N+1 queries:

```php
$this->relation('posts', resolver: fn ($posts) => PostResource::collection($posts)->resolve())
```

### Timestamps

```php
$this->timestamps()            // created_at, updated_at
$this->softDeleteTimestamps()  // created_at, updated_at, deleted_at
```

Each timestamp outputs the date in `Y-m-d H:i:s` format using the model's existing Carbon instance.

## Inertia integration

### Single resource

```php
return inertia('users/Show', [
    'user' => UserResource::make($user)->toInertia(),
]);
```

### Paginated collection

```php
return inertia('users/Index', [
    'users' => UserResource::collection(User::paginate())->toInertia(),
]);
```

The paginated shape is:

```php
[
    'data' => [...],
    'links' => [
        'first' => '...',
        'last'  => '...',
        'prev'  => null,
        'next'  => '...',
    ],
    'meta' => [
        'current_page' => 1,
        'from'         => 1,
        'last_page'    => 4,
        'path'         => 'https://app.test/users',
        'per_page'     => 15,
        'to'           => 15,
        'total'        => 50,
    ],
]
```

## Conventions

- Extend `AppResource`, not `JsonResource` directly.
- Keep `toArray()` focused on serialization only — no business logic or queries.
- Use `relation()` for loaded relations to prevent lazy loading.
- Use `toInertia()` when passing resources into `inertia()` props.
- Generate single resources with `php artisan make:resource UserResource`.
- Generate collections with `php artisan make:resource UserCollection --collection`.
