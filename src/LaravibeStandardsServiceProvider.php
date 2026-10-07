<?php

declare(strict_types=1);

namespace TheSoftPulze\LaravibeStandards;

use Illuminate\Support\ServiceProvider;
use TheSoftPulze\LaravibeStandards\Console\Commands\MakeDTOCommand;

class LaravibeStandardsServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__.'/../config/laravibe-standards.php', 'laravibe-standards');

        $this->app->singleton(LaravibeStandards::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        if (! $this->app->runningInConsole()) {
            return;
        }

        $this->publishes([
            __DIR__.'/../config/laravibe-standards.php' => config_path('laravibe-standards.php'),
        ], ['laravibe-standards', 'laravibe-standards-config']);

        $this->publishes([
            __DIR__.'/../../stubs/dto.stub' => base_path('stubs/laravibe-standards/dto.stub'),
            __DIR__.'/../../stubs/enum.stub' => base_path('stubs/enum.stub'),
            __DIR__.'/../../stubs/enum.backed.stub' => base_path('stubs/enum.backed.stub'),
            __DIR__.'/../../stubs/resource.stub' => base_path('stubs/resource.stub'),
            __DIR__.'/../../stubs/resource-collection.stub' => base_path('stubs/resource-collection.stub'),
        ], ['laravibe-standards', 'laravibe-standards-stubs']);

        $this->commands([
            MakeDTOCommand::class,
        ]);
    }
}
