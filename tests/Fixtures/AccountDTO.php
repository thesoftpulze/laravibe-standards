<?php

declare(strict_types=1);

namespace TheSoftPulze\LaravibeStandards\Tests\Fixtures;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\Support\Jsonable;
use TheSoftPulze\LaravibeStandards\DTOs\Concerns\AsDTO;

/**
 * @implements Arrayable<string, mixed>
 */
final readonly class AccountDTO implements Arrayable, Jsonable
{
    use AsDTO;

    public function __construct(
        public int $id,
        public float $balance,
        public bool $isActive,
        /** @var array<int, string> */
        public array $tags,
        public ?string $notes = null,
    ) {}
}
