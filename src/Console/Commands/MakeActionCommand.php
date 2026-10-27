<?php

declare(strict_types=1);

namespace TheSoftPulze\LaravibeStandards\Console\Commands;

use Illuminate\Console\Concerns\CreatesMatchingTest;
use Illuminate\Console\GeneratorCommand;
use Override;
use Symfony\Component\Console\Input\InputOption;

final class MakeActionCommand extends GeneratorCommand
{
    use CreatesMatchingTest;

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'make:action';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new action class';

    /**
     * The type of class being generated.
     *
     * @var string
     */
    protected $type = 'Action';

    /**
     * Get the stub file for the generator.
     */
    protected function getStub(): string
    {
        return $this->resolveStubPath('action.stub');
    }

    /**
     * Get the default namespace for the class.
     */
    #[Override]
    protected function getDefaultNamespace(mixed $rootNamespace): string
    {
        return $rootNamespace.'\\Actions';
    }

    /**
     * Get the console command options.
     *
     * @return array<array{string, string, int, string}>
     */
    #[Override]
    protected function getOptions(): array
    {
        return [
            ['force', 'f', InputOption::VALUE_NONE, 'Create the action even if the action already exists'],
        ];
    }

    /**
     * Resolve the fully-qualified path to the stub.
     */
    private function resolveStubPath(string $stub): string
    {
        $published = base_path('stubs/laravibe-standards/'.$stub);

        return file_exists($published)
            ? $published
            : __DIR__.'/../../../stubs/'.$stub;
    }
}
