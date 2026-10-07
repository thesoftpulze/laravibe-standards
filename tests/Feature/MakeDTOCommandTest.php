<?php

declare(strict_types=1);

use Illuminate\Support\Facades\File;

beforeEach(function () {
    File::deleteDirectory(app_path('DTOs'));
});

afterEach(function () {
    File::deleteDirectory(app_path('DTOs'));
});

it('generates a DTO class in the default namespace', function () {
    $this->artisan('make:dto', ['name' => 'UserDTO'])
        ->assertSuccessful();

    $path = app_path('DTOs/UserDTO.php');

    expect(File::exists($path))->toBeTrue();
    expect(File::get($path))->toContain('namespace App\DTOs;');
    expect(File::get($path))->toContain('final readonly class UserDTO');
    expect(File::get($path))->toContain('use TheSoftPulze\LaravibeStandards\DTOs\Concerns\AsDTO;');
});

it('generates a DTO with a nested namespace', function () {
    $this->artisan('make:dto', ['name' => 'Account/UpdateProfileDTO'])
        ->assertSuccessful();

    $path = app_path('DTOs/Account/UpdateProfileDTO.php');

    expect(File::exists($path))->toBeTrue();
    expect(File::get($path))->toContain('namespace App\DTOs\Account;');
    expect(File::get($path))->toContain('final readonly class UpdateProfileDTO');
});

it('overwrites an existing DTO with the --force flag', function () {
    File::ensureDirectoryExists(app_path('DTOs'));
    File::put(app_path('DTOs/UserDTO.php'), '<?php // original');

    $this->artisan('make:dto', ['name' => 'UserDTO', '--force' => true])
        ->assertSuccessful();

    expect(File::get(app_path('DTOs/UserDTO.php')))->toContain('final readonly class UserDTO');
});

it('fails when the DTO already exists without --force', function () {
    File::ensureDirectoryExists(app_path('DTOs'));
    File::put(app_path('DTOs/UserDTO.php'), '<?php // original');

    $this->artisan('make:dto', ['name' => 'UserDTO'])
        ->expectsOutputToContain('already exists');

    expect(File::get(app_path('DTOs/UserDTO.php')))->toBe('<?php // original');
});
