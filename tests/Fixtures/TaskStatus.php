<?php

declare(strict_types=1);

namespace TheSoftPulze\LaravibeStandards\Tests\Fixtures;

use TheSoftPulze\LaravibeStandards\Enums\Concerns\HasEnumMetadata;

enum TaskStatus: string
{
    use HasEnumMetadata;

    case InProgress = 'in_progress';
    case ON_HOLD = 'on_hold';
}
