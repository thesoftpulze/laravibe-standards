<?php

declare(strict_types=1);

namespace TheSoftPulze\LaravibeStandards\Tests\Fixtures;

use TheSoftPulze\LaravibeStandards\Enums\Concerns\HasEnumMetadata;

enum Priority
{
    use HasEnumMetadata;

    case Low;
    case Medium;
    case High;
}
