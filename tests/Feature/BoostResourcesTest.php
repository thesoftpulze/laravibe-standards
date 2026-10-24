<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\File;
use Illuminate\Support\ServiceProvider;
use Symfony\Component\Yaml\Yaml;
use TheSoftPulze\LaravibeStandards\DTOs\Concerns\AsDTO;
use TheSoftPulze\LaravibeStandards\Enums\Concerns\HasEnumMetadata;
use TheSoftPulze\LaravibeStandards\Resources\AppResource;
use TheSoftPulze\LaravibeStandards\Resources\AppResourceCollection;

$boostPath = __DIR__.'/../../resources/boost';

$skillFiles = glob($boostPath.'/skills/*/SKILL.md') ?: [];

$boostFiles = [$boostPath.'/guidelines/core.blade.php', ...$skillFiles];

it('ships a guideline that renders as Blade', function () use ($boostPath) {
    $rendered = Blade::render(File::get($boostPath.'/guidelines/core.blade.php'));

    expect($rendered)
        ->toContain('## Laravibe Standards')
        ->toContain('<code-snippet')
        ->not->toContain('@verbatim');
});

it('ships skills with valid frontmatter', function (string $file) {
    preg_match('/\A---\n(.*?)\n---\n/s', File::get($file), $matches);

    expect($matches)->not->toBeEmpty();

    /** @var array{name?: string, description?: string} $frontmatter */
    $frontmatter = Yaml::parse($matches[1]);

    expect($frontmatter['name'] ?? null)->toBe(basename(dirname($file)))
        ->and($frontmatter['description'] ?? '')->not->toBeEmpty();
})->with($skillFiles);

it('only references package classes that exist', function (string $file) {
    preg_match_all('/TheSoftPulze\\\\LaravibeStandards(?:\\\\\w+)+/', File::get($file), $matches);

    $missing = array_filter(
        array_unique($matches[0]),
        fn (string $class): bool => ! class_exists($class) && ! trait_exists($class),
    );

    expect($missing)->toBeEmpty();
})->with($boostFiles);

it('only references registered publish tags', function (string $file) {
    preg_match_all('/--tag="?([\w-]+)"?/', File::get($file), $matches);

    expect(array_diff($matches[1], ServiceProvider::publishableGroups()))->toBeEmpty();
})->with($boostFiles);

it('only documents helper methods the package provides', function (string $file) {
    $provided = collect([AppResource::class, AppResourceCollection::class, AsDTO::class, HasEnumMetadata::class])
        ->flatMap(fn (string $class): array => new ReflectionClass($class)->getMethods())
        ->map(fn (ReflectionMethod $method): string => $method->getName())
        ->all();

    $rows = array_filter(explode("\n", File::get($file)), fn (string $line): bool => str_starts_with($line, '| `'));

    preg_match_all('/(\w+)\(/', implode("\n", $rows), $matches);

    expect(array_diff($matches[1], $provided))->toBeEmpty();
})->with($skillFiles);
