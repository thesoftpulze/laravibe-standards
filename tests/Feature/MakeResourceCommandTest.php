<?php

declare(strict_types=1);

use Illuminate\Support\Facades\File;

beforeEach(function () {
    File::deleteDirectory(app_path('Http/Resources'));
    File::ensureDirectoryExists(app_path('Http/Resources'));
    File::makeDirectory(base_path('stubs'), 0755, true, true);
});

afterEach(function () {
    File::deleteDirectory(app_path('Http/Resources'));
    File::delete(base_path('stubs/resource.stub'));
    File::delete(base_path('stubs/resource-collection.stub'));
});

it('generates a single resource with package stub', function () {
    File::copy(__DIR__.'/../../stubs/resource.stub', base_path('stubs/resource.stub'));

    $this->artisan('make:resource', ['name' => 'UserResource'])
        ->assertSuccessful();

    $path = app_path('Http/Resources/UserResource.php');

    expect(File::exists($path))->toBeTrue();
    expect(File::get($path))->toContain('use TheSoftPulze\LaravibeStandards\Resources\AppResource;');
    expect(File::get($path))->toContain('final class UserResource extends AppResource');
});

it('generates a collection resource with package stub', function () {
    File::copy(__DIR__.'/../../stubs/resource-collection.stub', base_path('stubs/resource-collection.stub'));

    $this->artisan('make:resource', ['name' => 'UserCollection', '--collection' => true])
        ->assertSuccessful();

    $path = app_path('Http/Resources/UserCollection.php');

    expect(File::exists($path))->toBeTrue();
    expect(File::get($path))->toContain('use TheSoftPulze\LaravibeStandards\Resources\AppResourceCollection;');
    expect(File::get($path))->toContain('final class UserCollection extends AppResourceCollection');
});
