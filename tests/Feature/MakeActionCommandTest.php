<?php

declare(strict_types=1);

use Illuminate\Support\Facades\File;

beforeEach(function () {
    File::deleteDirectory(app_path('Actions'));
    File::deleteDirectory(base_path('stubs/laravibe-standards'));
    File::deleteDirectory(base_path('tests/Feature/Actions'));
});

afterEach(function () {
    File::deleteDirectory(app_path('Actions'));
    File::deleteDirectory(base_path('stubs/laravibe-standards'));
    File::deleteDirectory(base_path('tests/Feature/Actions'));
});

it('generates an action class in the default namespace', function () {
    $this->artisan('make:action', ['name' => 'CreateInvoiceAction'])
        ->expectsOutputToContain('Action')
        ->assertSuccessful();

    $path = app_path('Actions/CreateInvoiceAction.php');

    expect(File::exists($path))->toBeTrue();
    expect(File::get($path))
        ->toContain('declare(strict_types=1);')
        ->toContain('namespace App\Actions;')
        ->toContain('final readonly class CreateInvoiceAction')
        ->toContain('public function handle(): void');
});

it('generates an action with a nested namespace', function () {
    $this->artisan('make:action', ['name' => 'Account/UpdateProfileAction'])
        ->assertSuccessful();

    $path = app_path('Actions/Account/UpdateProfileAction.php');

    expect(File::exists($path))->toBeTrue();
    expect(File::get($path))
        ->toContain('namespace App\Actions\Account;')
        ->toContain('final readonly class UpdateProfileAction');
});

it('uses the published stub when present', function () {
    File::ensureDirectoryExists(base_path('stubs/laravibe-standards'));
    File::put(base_path('stubs/laravibe-standards/action.stub'), "<?php\n\nnamespace {{ namespace }};\n\n// custom\nclass {{ class }} {}\n");

    $this->artisan('make:action', ['name' => 'CreateInvoiceAction'])
        ->assertSuccessful();

    expect(File::get(app_path('Actions/CreateInvoiceAction.php')))
        ->toContain('// custom')
        ->toContain('class CreateInvoiceAction {}');
});

it('generates a matching Pest test with the --pest flag', function () {
    $this->artisan('make:action', ['name' => 'Account/UpdateProfileAction', '--pest' => true])
        ->assertSuccessful();

    expect(File::exists(base_path('tests/Feature/Actions/Account/UpdateProfileActionTest.php')))->toBeTrue();
});

it('overwrites an existing action with the --force flag', function () {
    File::ensureDirectoryExists(app_path('Actions'));
    File::put(app_path('Actions/CreateInvoiceAction.php'), '<?php // original');

    $this->artisan('make:action', ['name' => 'CreateInvoiceAction', '--force' => true])
        ->assertSuccessful();

    expect(File::get(app_path('Actions/CreateInvoiceAction.php')))->toContain('final readonly class CreateInvoiceAction');
});

it('fails when the action already exists without --force', function () {
    File::ensureDirectoryExists(app_path('Actions'));
    File::put(app_path('Actions/CreateInvoiceAction.php'), '<?php // original');

    $this->artisan('make:action', ['name' => 'CreateInvoiceAction'])
        ->expectsOutputToContain('Action already exists');

    expect(File::get(app_path('Actions/CreateInvoiceAction.php')))->toBe('<?php // original');
});
