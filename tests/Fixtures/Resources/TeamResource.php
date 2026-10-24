<?php

declare(strict_types=1);

namespace TheSoftPulze\LaravibeStandards\Tests\Fixtures\Resources;

use Illuminate\Http\Request;
use TheSoftPulze\LaravibeStandards\Resources\AppResource;
use TheSoftPulze\LaravibeStandards\Resources\AppResourceCollection;

final class TeamResource extends AppResource
{
    /**
     * @return array<int|string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            $this->id(),
            $this->relation('members', fn (mixed $members): AppResourceCollection => UserResource::collection($members)),
        ];
    }
}
