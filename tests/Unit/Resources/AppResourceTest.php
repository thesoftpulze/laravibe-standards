<?php

declare(strict_types=1);

use TheSoftPulze\LaravibeStandards\Resources\AppResource;
use TheSoftPulze\LaravibeStandards\Resources\AppResourceCollection;

it('resolves to a plain array via toInertia', function () {
    $model = new class(['id' => 1, 'name' => 'Alice'])
    {
        public function __construct(private array $attributes) {}

        public function getAttributes(): array
        {
            return $this->attributes;
        }

        public function relationLoaded(string $key): bool
        {
            return false;
        }

        public function __get(string $key): mixed
        {
            return $this->attributes[$key] ?? null;
        }

        public function __isset(string $key): bool
        {
            return isset($this->attributes[$key]);
        }
    };

    $resource = new class($model) extends AppResource
    {
        public static $wrap = null;

        public function toArray($request): array
        {
            return [$this->id(), $this->attribute('name')];
        }
    };

    $result = $resource->toInertia();

    expect($result)->toBe(['id' => 1, 'name' => 'Alice']);
});

it('creates an AppResourceCollection via newCollection', function () {
    $collection = AppResource::collection([]);

    expect($collection)->toBeInstanceOf(AppResourceCollection::class);
});
