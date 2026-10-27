<?php

declare(strict_types=1);

use Illuminate\Support\ServiceProvider;
use TheSoftPulze\LaravibeStandards\LaravibeStandardsServiceProvider;

$expectedStubs = [
    'action.stub' => 'stubs/laravibe-standards/action.stub',
    'dto.stub' => 'stubs/laravibe-standards/dto.stub',
    'enum.stub' => 'stubs/enum.stub',
    'enum.backed.stub' => 'stubs/enum.backed.stub',
    'resource.stub' => 'stubs/resource.stub',
    'resource-collection.stub' => 'stubs/resource-collection.stub',
];

it('publishes every package stub to its target path', function (string $tag) use ($expectedStubs) {
    $paths = ServiceProvider::pathsToPublish(LaravibeStandardsServiceProvider::class, $tag);

    $published = [];

    foreach ($paths as $source => $target) {
        expect($source)->toBeFile();

        $published[basename($source)] = $target;
    }

    expect($published)->toEqual(array_map(base_path(...), $expectedStubs));
})->with(['laravibe-standards', 'laravibe-standards-stubs']);
