<?php

declare(strict_types=1);

namespace TheSoftPulze\LaravibeStandards\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \TheSoftPulze\LaravibeStandards\LaravibeStandards
 */
class LaravibeStandards extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return \TheSoftPulze\LaravibeStandards\LaravibeStandards::class;
    }
}
