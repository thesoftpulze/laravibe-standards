<?php

declare(strict_types=1);

namespace TheSoftPulze\LaravibeStandards\Console\Commands;

use Illuminate\Console\GeneratorCommand;
use Override;
use Symfony\Component\Console\Input\InputOption;

final class MakeDTOCommand extends GeneratorCommand
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'make:dto';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new DTO class';

    /**
     * The type of class being generated.
     *
     * @var string
     */
    protected $type = 'Class';

    /**
     * Get the stub file for the generator.
     */
    protected function getStub(): string
    {
        return $this->resolveStubPath('dto.stub');
    }

    /**
     * Get the default namespace for the class.
     */
    #[Override]
    protected function getDefaultNamespace(mixed $rootNamespace): string
    {
        return $rootNamespace.'\\DTOs';
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
            ['force', 'f', InputOption::VALUE_NONE, 'Create the DTO class even if the class already exists'],
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
