<?php

declare(strict_types=1);

use Illuminate\Support\Carbon;
use TheSoftPulze\LaravibeStandards\Resources\AppResource;

function makeModel(array $attributes = [], array $loadedRelations = []): object
{
    return new class($attributes, $loadedRelations)
    {
        public function __construct(
            private array $attributes,
            private array $loadedRelations,
        ) {}

        public function getAttributes(): array
        {
            return $this->attributes;
        }

        public function relationLoaded(string $key): bool
        {
            return array_key_exists($key, $this->loadedRelations);
        }

        public function __get(string $key): mixed
        {
            if (array_key_exists($key, $this->attributes)) {
                return $this->attributes[$key];
            }

            if (array_key_exists($key, $this->loadedRelations)) {
                return $this->loadedRelations[$key];
            }

            return null;
        }

        public function __isset(string $key): bool
        {
            return isset($this->attributes[$key]) || isset($this->loadedRelations[$key]);
        }
    };
}

it('resolves id attribute', function () {
    $resource = new class(makeModel(['id' => 42])) extends AppResource
    {
        public static $wrap = null;

        public function toArray($request): array
        {
            return [$this->id()];
        }
    };

    expect($resource->resolve())->toHaveKey('id', 42);
});

it('resolves a present attribute', function () {
    $resource = new class(makeModel(['name' => 'Alice'])) extends AppResource
    {
        public static $wrap = null;

        public function toArray($request): array
        {
            return [$this->attribute('name')];
        }
    };

    expect($resource->resolve())->toHaveKey('name', 'Alice');
});

it('includes an optional attribute when key is present', function () {
    $resource = new class(makeModel(['bio' => 'Developer'])) extends AppResource
    {
        public static $wrap = null;

        public function toArray($request): array
        {
            return [$this->optionalAttribute('bio')];
        }
    };

    expect($resource->resolve())->toHaveKey('bio', 'Developer');
});

it('omits an optional attribute when key is absent', function () {
    $resource = new class(makeModel([])) extends AppResource
    {
        public static $wrap = null;

        public function toArray($request): array
        {
            return [$this->optionalAttribute('missing')];
        }
    };

    expect($resource->resolve())->not->toHaveKey('missing');
});

it('uses resolver closure for attribute', function () {
    $resource = new class(makeModel(['status' => 'active'])) extends AppResource
    {
        public static $wrap = null;

        public function toArray($request): array
        {
            return [$this->attribute('status', resolver: fn ($v) => strtoupper($v))];
        }
    };

    expect($resource->resolve())->toHaveKey('status', 'ACTIVE');
});

it('supports alias prefix and suffix on attribute', function () {
    $resource = new class(makeModel(['name' => 'Alice'])) extends AppResource
    {
        public static $wrap = null;

        public function toArray($request): array
        {
            return [$this->attribute('name', alias: 'display', prefix: 'user_', suffix: '_label')];
        }
    };

    expect($resource->resolve())->toHaveKey('user_display_label', 'Alice');
});

it('includes relation when eager loaded', function () {
    $resource = new class(makeModel([], ['posts' => ['Post A', 'Post B']])) extends AppResource
    {
        public static $wrap = null;

        public function toArray($request): array
        {
            return [$this->relation('posts')];
        }
    };

    expect($resource->resolve())->toHaveKey('posts', ['Post A', 'Post B']);
});

it('omits relation when not eager loaded', function () {
    $resource = new class(makeModel([])) extends AppResource
    {
        public static $wrap = null;

        public function toArray($request): array
        {
            return [$this->relation('posts')];
        }
    };

    expect($resource->resolve())->not->toHaveKey('posts');
});

it('resolves timestamps', function () {
    $now = Carbon::create(2026, 7, 24, 12, 0, 0);
    $later = Carbon::create(2026, 7, 25, 14, 30, 0);

    $resource = new class(makeModel(['created_at' => $now, 'updated_at' => $later])) extends AppResource
    {
        public static $wrap = null;

        public function toArray($request): array
        {
            return [...$this->timestamps()];
        }
    };

    $result = $resource->resolve();

    expect($result)->toHaveKey('created_at', '2026-07-24 12:00:00');
    expect($result)->toHaveKey('updated_at', '2026-07-25 14:30:00');
});

it('resolves soft delete timestamps', function () {
    $created = Carbon::create(2026, 7, 24, 12, 0, 0);
    $updated = Carbon::create(2026, 7, 25, 14, 30, 0);
    $deleted = Carbon::create(2026, 7, 26, 10, 0, 0);

    $resource = new class(makeModel(['created_at' => $created, 'updated_at' => $updated, 'deleted_at' => $deleted])) extends AppResource
    {
        public static $wrap = null;

        public function toArray($request): array
        {
            return [...$this->softDeleteTimestamps()];
        }
    };

    $result = $resource->resolve();

    expect($result)->toHaveKey('created_at', '2026-07-24 12:00:00');
    expect($result)->toHaveKey('updated_at', '2026-07-25 14:30:00');
    expect($result)->toHaveKey('deleted_at', '2026-07-26 10:00:00');
});

it('handles absent timestamps gracefully', function () {
    $resource = new class(makeModel(['name' => 'Alice'])) extends AppResource
    {
        public static $wrap = null;

        public function toArray($request): array
        {
            return [
                $this->attribute('name'),
                ...$this->timestamps(),
            ];
        }
    };

    $result = $resource->resolve();

    expect($result)->toHaveKey('name', 'Alice');
    expect($result)->not->toHaveKey('created_at');
    expect($result)->not->toHaveKey('updated_at');
});
