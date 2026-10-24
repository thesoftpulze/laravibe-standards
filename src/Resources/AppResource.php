<?php

declare(strict_types=1);

namespace TheSoftPulze\LaravibeStandards\Resources;

use Illuminate\Http\Resources\Attributes\PreserveKeys;
use Illuminate\Http\Resources\Json\JsonResource;
use Override;
use ReflectionClass;
use TheSoftPulze\LaravibeStandards\Resources\Concerns\FlexibleJsonResource;

abstract class AppResource extends JsonResource
{
    use FlexibleJsonResource;

    /**
     * Resolve the resource to a plain array for Inertia props.
     *
     * @return array<int|string, mixed>
     */
    final public function toInertia(): array
    {
        return $this->resolve();
    }

    /**
     * Create a new resource collection instance.
     *
     * Mirrors JsonResource::collection() with a narrowed return type, so static analysis
     * sees toInertia() on UserResource::collection($users).
     */
    #[Override]
    public static function collection(mixed $resource): AppResourceCollection
    {
        $collection = static::newCollection($resource);
        $reflection = new ReflectionClass(static::class);

        $collection->preserveKeys = $reflection->getAttributes(PreserveKeys::class) !== []
            || ($reflection->getDefaultProperties()['preserveKeys'] ?? false) === true;

        return $collection;
    }

    /**
     * Create a new resource collection instance.
     */
    #[Override]
    protected static function newCollection(mixed $resource): AppResourceCollection
    {
        return new AppResourceCollection($resource, static::class);
    }
}
