<?php

declare(strict_types=1);

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Pagination\LengthAwarePaginator;
use TheSoftPulze\LaravibeStandards\Resources\AppResourceCollection;

it('wraps items via toInertia when collects is set', function () {
    $resource = new class(new stdClass) extends JsonResource
    {
        public static $wrap = null;

        public function toArray($request): array
        {
            return ['id' => $this->resource->id ?? null];
        }
    };

    $resource->resource->id = 42;

    $collection = new AppResourceCollection([$resource], $resource::class);

    $result = $collection->toInertia();

    expect($result)->toBe([['id' => 42]]);
});

it('resolves a plain collection without collects', function () {
    $item = new class
    {
        public function toArray($request): array
        {
            return ['value' => 1];
        }
    };

    $collection = new AppResourceCollection([$item]);

    $result = $collection->toInertia();

    expect($result)->toBe([['value' => 1]]);
});

it('returns pagination information for a paginated collection', function () {
    $items = [
        new class
        {
            public function toArray($request): array
            {
                return ['id' => 1];
            }
        },
        new class
        {
            public function toArray($request): array
            {
                return ['id' => 2];
            }
        },
    ];

    $paginator = new LengthAwarePaginator(
        items: $items,
        total: 10,
        perPage: 2,
        currentPage: 1,
        options: ['path' => 'http://localhost'],
    );

    $collection = new AppResourceCollection($paginator);

    $result = $collection->toInertia();

    expect($result)->toHaveKey('data');
    expect($result)->toHaveKey('links');
    expect($result)->toHaveKey('meta');
    expect($result['meta'])->toHaveKey('current_page', 1);
    expect($result['meta'])->toHaveKey('per_page', 2);
    expect($result['meta'])->toHaveKey('total', 10);
});
