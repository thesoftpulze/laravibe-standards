<?php

declare(strict_types=1);

namespace TheSoftPulze\LaravibeStandards\Tests\Fixtures\Resources;

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
            $this->attribute('name', alias: 'display_name'),
        ];
    }
}
