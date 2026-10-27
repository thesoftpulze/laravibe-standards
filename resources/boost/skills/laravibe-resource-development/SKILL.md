---
name: laravibe-resource-development
description: Create, edit, or refactor API resources and resource collections in app/Http/Resources using AppResource and AppResourceCollection from thesoftpulze/laravibe-standards, including field helpers, eager-loaded relations, pagination, and Inertia props.
license: MIT
metadata:
  author: Ashok Barua
---

# Laravibe Resource Development

## When to use this skill

Use this skill when creating or changing an API resource or resource collection, returning models from a JSON endpoint, or passing models, collections, or paginators to Inertia pages.

## Create a resource

```bash
php artisan make:resource UserResource
php artisan make:resource UserCollection --collection
```

`make:resource` is Laravel's own command. It only extends the package base classes once the package stubs are published. If `stubs/resource.stub` doesn't exist in the application, run this first, or change the parent class by hand:

```bash
php artisan vendor:publish --tag=laravibe-standards-stubs
```

```php
<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Request;
use TheSoftPulze\LaravibeStandards\Resources\AppResource;

final class UserResource extends AppResource
{
    /**
     * @return array<int|string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            $this->id(),
            $this->attribute('name'),
            $this->attribute('email'),
            $this->optionalAttribute('email_verified_at'),
            $this->attribute('status', resolver: fn (UserStatus $status): array => $status->toOption()),
            $this->relation('posts', fn ($posts) => PostResource::collection($posts)),
            ...$this->timestamps(),
        ];
    }
}
```

## Rules

- Place resources in `app/Http/Resources`, using subfolders for domains such as `Admin/`.
- Make resources `final`. Single resources extend `TheSoftPulze\LaravibeStandards\Resources\AppResource` and end with `Resource`.
- Collections extend `TheSoftPulze\LaravibeStandards\Resources\AppResourceCollection` and end with `Collection`.
- Return a list of field helpers from `toArray()`. Spread the helpers that return several fields (`timestamps()`, `softDeleteTimestamps()`).
- Keep `toArray()` to serialization. Load relations and run queries in the controller or query, not in the resource.

## Field helpers

| Helper                                      | Output                                                                       |
| ------------------------------------------- | ---------------------------------------------------------------------------- |
| `id()`                                      | `id`                                                                         |
| `attribute('name')`                         | `name`, always present (`null` if missing)                                   |
| `optionalAttribute('email_verified_at')`    | only when the attribute was selected on the model                            |
| `relation('posts')`                         | only when the relation is eager loaded, so it never causes a lazy-load query |
| `createdAt()`, `updatedAt()`, `deletedAt()` | the timestamp as `Y-m-d H:i:s`, only when selected                           |
| `timestamps()`                              | `createdAt()` and `updatedAt()`                                              |
| `softDeleteTimestamps()`                    | `timestamps()` and `deletedAt()`                                             |

`attribute()`, `optionalAttribute()`, and `relation()` also accept:

- `resolver`: a closure that transforms the value, for example `fn (Carbon $date): string => $date->toDateString()`.
- `alias`: the output key, for example `alias: 'display_name'`.
- `prefix` and `suffix`: added around the output key.

Nested resources and collections returned from a resolver are resolved to arrays automatically.

## Collections

- `UserResource::collection($users)` collects `UserResource`. It needs no collection class.
- A `UserCollection` class collects `UserResource` from the same namespace automatically.
- When the collection name doesn't follow that pattern, set `public $collects = UserResource::class;`.
- To keep the input array keys, add Laravel's `#[PreserveKeys]` attribute to the resource class, or call `->preserveKeys()` on the collection.

## Responses and Inertia

```php
// JSON endpoints: return the resource as usual.
return UserResource::make($user->load('posts'));
return UserResource::collection(User::query()->paginate());

// Inertia: pass plain arrays with toInertia().
return inertia('users/Show', [
    'user' => UserResource::make($user)->toInertia(),
]);

return inertia('users/Index', [
    'users' => UserResource::collection(User::query()->paginate())->toInertia(),
]);
```

`toInertia()` on a single resource returns its fields without the `data` wrapper. On a paginated collection it returns `data`, `links` (`first`, `last`, `prev`, `next`), and `meta` (`current_page`, `from`, `last_page`, `path`, `per_page`, `to`, `total`). On a plain collection it returns the list of items.

## Anti-patterns

- Extending `JsonResource` or `ResourceCollection` directly.
- Reading a relation with `$this->posts` in `toArray()`. Use `relation('posts')` so unloaded relations are skipped.
- Queries, authorization, or business logic in `toArray()`.
- Passing a resource object to `inertia()` without calling `toInertia()`.
