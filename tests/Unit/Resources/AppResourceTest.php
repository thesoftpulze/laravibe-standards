<?php

declare(strict_types=1);

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Auth\User;
use Illuminate\Http\Resources\Attributes\PreserveKeys;
use TheSoftPulze\LaravibeStandards\Resources\AppResource;
use TheSoftPulze\LaravibeStandards\Resources\AppResourceCollection;
use TheSoftPulze\LaravibeStandards\Tests\Fixtures\Resources\TeamResource;

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

it('resolves a nested resource collection returned from a relation resolver', function () {
    $user = new User;
    $user->forceFill(['id' => 7, 'name' => 'Alice']);

    $team = new User;
    $team->forceFill(['id' => 1]);
    $team->setRelation('members', new Collection([$user]));

    expect(TeamResource::make($team)->toInertia())->toBe([
        'id' => 1,
        'members' => [['id' => 7, 'display_name' => 'Alice']],
    ]);
});

it('omits a relation that is not loaded', function () {
    $team = new User;
    $team->forceFill(['id' => 1]);

    expect(TeamResource::make($team)->toInertia())->toBe(['id' => 1]);
});

it('preserves collection keys when the resource has the PreserveKeys attribute', function () {
    $first = new User;
    $first->forceFill(['id' => 1]);

    $second = new User;
    $second->forceFill(['id' => 2]);

    $resource = new #[PreserveKeys] class(null) extends AppResource
    {
        public function toArray($request): array
        {
            return [$this->id()];
        }
    };

    $collection = $resource::collection(['first' => $first, 'second' => $second]);

    expect($collection)->toBeInstanceOf(AppResourceCollection::class)
        ->and($collection->toInertia())->toBe(['first' => ['id' => 1], 'second' => ['id' => 2]]);
});
