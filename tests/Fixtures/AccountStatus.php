<?php

declare(strict_types=1);

namespace TheSoftPulze\LaravibeStandards\Tests\Fixtures;

enum AccountStatus: int
{
    case Active = 1;
    case Inactive = 2;
    case Suspended = 3;
}
