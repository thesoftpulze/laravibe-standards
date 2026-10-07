<?php

declare(strict_types=1);

namespace TheSoftPulze\LaravibeStandards\Tests\Fixtures;

use TheSoftPulze\LaravibeStandards\Enums\Concerns\HasEnumMetadata;

enum ToastType: int
{
    use HasEnumMetadata;

    case Error = 1;
    case Success = 2;
    case Warning = 3;
    case Info = 4;
}
