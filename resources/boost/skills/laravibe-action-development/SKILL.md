---
name: laravibe-action-development
description: Create, edit, or refactor single-purpose action classes in app/Actions generated with the make:action command from thesoftpulze/laravibe-standards, including moving business logic out of controllers, jobs, and commands.
license: MIT
metadata:
  author: Ashok Barua Akas
---

# Laravibe Action Development

## When to use this skill

Use this skill when creating an action, moving business logic out of a controller, job, listener, or command, or calling one operation from several entry points.

## Create an action

```bash
php artisan make:action Account/UpdateProfileAction
php artisan make:action Account/UpdateProfileAction --pest   # also creates tests/Feature/Actions/Account/UpdateProfileActionTest.php
```

This creates `app/Actions/Account/UpdateProfileAction.php` with an empty `handle(): void` method. Replace its signature with the real inputs and return type. `--test`, `--pest`, and `--phpunit` also generate a matching test, and `--force` overwrites an existing class.

If `stubs/laravibe-standards/action.stub` exists in the application, the command uses it instead of the package stub. Publish it to customize generated actions:

```bash
php artisan vendor:publish --tag=laravibe-standards-stubs
```

```php
<?php

declare(strict_types=1);

namespace App\Actions\Account;

use App\DTOs\Account\UpdateProfileDTO;
use App\Models\User;
use Illuminate\Database\DatabaseManager;

final readonly class UpdateProfileAction
{
    public function __construct(
        private DatabaseManager $database,
    ) {}

    /**
     * Update the user's profile and their public card.
     */
    public function handle(User $user, UpdateProfileDTO $data): User
    {
        return $this->database->transaction(function () use ($user, $data): User {
            $user->update($data->toEloquent());
            $user->card()->update(['name' => $data->name]);

            return $user;
        });
    }
}
```

## Rules

- Place actions in `app/Actions` or `app/Actions/{Domain}`. Name them verb first and end with `Action`, for example `CreateInvoiceAction`.
- Make every action `final readonly` with one public `handle()` method. Split unrelated work into separate actions.
- Type every parameter and the return type. Pass a DTO instead of a loose array when there are more than a few inputs.
- Inject services and other actions through the constructor as `private` promoted properties. Keep per-call data in `handle()` arguments.
- Wrap writes to several records in a database transaction.
- Classes that implement framework contracts (for example Fortify's `CreatesNewUsers`) keep the method name the contract requires.

## Call an action

Resolve actions from the container so their constructor dependencies are injected:

```php
public function update(UpdateProfileRequest $request, UpdateProfileAction $action): RedirectResponse
{
    $action->handle($request->user(), UpdateProfileDTO::from($request));

    return back();
}
```

In jobs, listeners, and commands, type-hint the action on `handle()`. Elsewhere use `app(UpdateProfileAction::class)`.

## Test

Test the action directly instead of only through HTTP:

```php
it('updates the profile', function () {
    $user = User::factory()->create();

    app(UpdateProfileAction::class)->handle($user, UpdateProfileDTO::from(['name' => 'Taylor']));

    expect($user->refresh()->name)->toBe('Taylor');
});
```

## Anti-patterns

- Validating input inside an action. Validate in a `FormRequest` and pass the validated data in.
- Reading `request()`, `auth()`, or the session inside an action. Pass the user and values in as arguments.
- Returning HTTP responses, redirects, or Inertia pages from an action.
- Several public methods, or a `handle()` that branches into unrelated operations.
- Static `handle()` methods or `new UpdateProfileAction()`: they bypass constructor injection.
