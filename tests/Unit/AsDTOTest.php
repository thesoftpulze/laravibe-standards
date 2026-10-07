<?php

declare(strict_types=1);

use Carbon\CarbonImmutable;
use Illuminate\Http\Request;
use TheSoftPulze\LaravibeStandards\Tests\Fixtures\AccountDTO;
use TheSoftPulze\LaravibeStandards\Tests\Fixtures\AccountStatus;
use TheSoftPulze\LaravibeStandards\Tests\Fixtures\ContactDTO;
use TheSoftPulze\LaravibeStandards\Tests\Fixtures\StrictDTO;
use TheSoftPulze\LaravibeStandards\Tests\Fixtures\UserProfileDTO;
use TheSoftPulze\LaravibeStandards\Tests\Fixtures\UserRole;

// from() / fromArray()

it('hydrates from an array', function () {
    $dto = UserProfileDTO::fromArray([
        'name' => 'Alice',
        'bio' => 'Developer',
    ]);

    expect($dto->name)->toBe('Alice');
    expect($dto->bio)->toBe('Developer');
});

it('uses default values for missing optional parameters', function () {
    $dto = UserProfileDTO::fromArray(['name' => 'Alice']);

    expect($dto->name)->toBe('Alice');
    expect($dto->bio)->toBeNull();
});

it('throws when a required parameter is missing', function () {
    UserProfileDTO::fromArray([]);
})->throws(InvalidArgumentException::class, 'Missing required property [name]');

it('throws when a non-nullable parameter receives null', function () {
    UserProfileDTO::fromArray(['name' => null]);
})->throws(InvalidArgumentException::class, 'does not allow null');

it('accepts null for nullable parameters', function () {
    $dto = UserProfileDTO::fromArray(['name' => 'Alice', 'bio' => null]);

    expect($dto->bio)->toBeNull();
});

it('ignores unknown keys by default', function () {
    $dto = UserProfileDTO::fromArray([
        'name' => 'Alice',
        'unknown' => 'should be ignored',
    ]);

    expect($dto->name)->toBe('Alice');
});

it('throws on unknown keys when shouldThrowOnUnknownKeys is true', function () {
    StrictDTO::fromArray(['name' => 'Test', 'value' => 1, 'extra' => 'bad']);
})->throws(InvalidArgumentException::class, 'Unknown properties');

// from()

it('hydrates from an array via from()', function () {
    $dto = UserProfileDTO::from(['name' => 'Bob']);

    expect($dto->name)->toBe('Bob');
});

it('hydrates from a request via from()', function () {
    $request = new Request(['name' => 'Charlie']);

    $dto = UserProfileDTO::from($request);

    expect($dto->name)->toBe('Charlie');
});

// fromRequest()

it('hydrates from a request using validated()', function () {
    $request = Request::create('/', 'POST', ['name' => 'Charlie']);
    $request->setRouteResolver(fn () => null);

    $dto = UserProfileDTO::fromRequest($request);

    expect($dto->name)->toBe('Charlie');
});

// toArray()

it('serializes to an array', function () {
    $dto = UserProfileDTO::fromArray(['name' => 'Alice', 'bio' => 'Dev']);

    expect($dto->toArray())->toBe([
        'name' => 'Alice',
        'bio' => 'Dev',
    ]);
});

// toJson()

it('serializes to JSON', function () {
    $dto = UserProfileDTO::fromArray(['name' => 'Alice']);

    $json = $dto->toJson();

    expect($json)->toBe(json_encode(['name' => 'Alice', 'bio' => null]));
});

// toEloquent()

it('serializes to an Eloquent array', function () {
    $dto = UserProfileDTO::fromArray(['name' => 'Alice', 'bio' => 'Dev']);

    expect($dto->toEloquent())->toBe([
        'name' => 'Alice',
        'bio' => 'Dev',
    ]);
});

// forEloquent()

it('merges extras via forEloquent', function () {
    $dto = UserProfileDTO::fromArray(['name' => 'Alice']);

    $result = $dto->forEloquent(['source' => 'api']);

    expect($result)->toHaveKey('name', 'Alice');
    expect($result)->toHaveKey('source', 'api');
});

// with()

it('creates a new instance with overrides', function () {
    $original = UserProfileDTO::fromArray(['name' => 'Alice', 'bio' => 'Dev']);
    $updated = $original->with(['bio' => 'Senior Dev']);

    expect($original->bio)->toBe('Dev');
    expect($updated->bio)->toBe('Senior Dev');
});

// Type casting

it('casts int from string', function () {
    $dto = AccountDTO::fromArray([
        'id' => '42',
        'balance' => 100.0,
        'isActive' => true,
        'tags' => [],
    ]);

    expect($dto->id)->toBe(42)->toBeInt();
});

it('casts float from string', function () {
    $dto = AccountDTO::fromArray([
        'id' => 1,
        'balance' => '99.99',
        'isActive' => true,
        'tags' => [],
    ]);

    expect($dto->balance)->toBe(99.99)->toBeFloat();
});

it('casts bool from string', function () {
    $dto = AccountDTO::fromArray([
        'id' => 1,
        'balance' => 0.0,
        'isActive' => '1',
        'tags' => [],
    ]);

    expect($dto->isActive)->toBeTrue();
});

it('casts bool from int', function () {
    $dto = AccountDTO::fromArray([
        'id' => 1,
        'balance' => 0.0,
        'isActive' => 1,
        'tags' => [],
    ]);

    expect($dto->isActive)->toBeTrue();
});

it('casts string from int', function () {
    $dto = AccountDTO::fromArray([
        'id' => '42',
        'balance' => 0.0,
        'isActive' => false,
        'tags' => [],
    ]);

    expect($dto->id)->toBeInt();
});

it('accepts array type', function () {
    $dto = AccountDTO::fromArray([
        'id' => 1,
        'balance' => 0.0,
        'isActive' => false,
        'tags' => ['a', 'b'],
    ]);

    expect($dto->tags)->toBe(['a', 'b']);
});

// Enum casting

it('casts backed enum from int value', function () {
    $dto = ContactDTO::fromArray([
        'email' => 'test@example.com',
        'registeredAt' => '2024-01-15T10:00:00+00:00',
        'status' => 1,
        'role' => 'Admin',
        'profile' => ['name' => 'Alice'],
    ]);

    expect($dto->status)->toBe(AccountStatus::Active);
});

it('casts unit enum from string name', function () {
    $dto = ContactDTO::fromArray([
        'email' => 'test@example.com',
        'registeredAt' => '2024-01-15T10:00:00+00:00',
        'status' => 1,
        'role' => 'Admin',
        'profile' => ['name' => 'Alice'],
    ]);

    expect($dto->role)->toBe(UserRole::Admin);
});

// DateTime casting

it('casts DateTimeImmutable from string', function () {
    $dto = ContactDTO::fromArray([
        'email' => 'test@example.com',
        'registeredAt' => '2024-01-15T10:00:00+00:00',
        'status' => 1,
        'role' => 'Admin',
        'profile' => ['name' => 'Alice'],
    ]);

    expect($dto->registeredAt)->toBeInstanceOf(DateTimeImmutable::class);
});

it('casts DateTimeImmutable from Carbon instance', function () {
    $carbon = new CarbonImmutable('2024-06-15T12:00:00+00:00');

    $dto = ContactDTO::fromArray([
        'email' => 'test@example.com',
        'registeredAt' => $carbon,
        'status' => 1,
        'role' => 'Admin',
        'profile' => ['name' => 'Alice'],
    ]);

    expect($dto->registeredAt)->toBeInstanceOf(DateTimeImmutable::class);
    expect($dto->registeredAt->format('c'))->toBe('2024-06-15T12:00:00+00:00');
});

// Nested DTO casting

it('casts nested DTO from array', function () {
    $dto = ContactDTO::fromArray([
        'email' => 'test@example.com',
        'registeredAt' => '2024-01-15T10:00:00+00:00',
        'status' => 1,
        'role' => 'Admin',
        'profile' => ['name' => 'Alice', 'bio' => 'Developer'],
    ]);

    expect($dto->profile)->toBeInstanceOf(UserProfileDTO::class);
    expect($dto->profile->name)->toBe('Alice');
    expect($dto->profile->bio)->toBe('Developer');
});

it('passes through an existing DTO instance for nested DTO', function () {
    $profile = UserProfileDTO::fromArray(['name' => 'Bob']);
    $dto = ContactDTO::fromArray([
        'email' => 'test@example.com',
        'registeredAt' => '2024-01-15T10:00:00+00:00',
        'status' => 1,
        'role' => 'Admin',
        'profile' => $profile,
    ]);

    expect($dto->profile)->toBe($profile);
});

// Enum toArray normalization

it('serializes backed enum to its value in toArray', function () {
    $dto = ContactDTO::fromArray([
        'email' => 'test@example.com',
        'registeredAt' => '2024-01-15T10:00:00+00:00',
        'status' => 1,
        'role' => 'Admin',
        'profile' => ['name' => 'Alice'],
    ]);

    $array = $dto->toArray();

    expect($array['status'])->toBe(1);
});

it('serializes unit enum to its name in toArray', function () {
    $dto = ContactDTO::fromArray([
        'email' => 'test@example.com',
        'registeredAt' => '2024-01-15T10:00:00+00:00',
        'status' => 1,
        'role' => 'Admin',
        'profile' => ['name' => 'Alice'],
    ]);

    $array = $dto->toArray();

    expect($array['role'])->toBe('Admin');
});

// DateTime toEloquent normalization

it('formats DateTime as Y-m-d H:i:s in toEloquent', function () {
    $dto = ContactDTO::fromArray([
        'email' => 'test@example.com',
        'registeredAt' => '2024-01-15T10:00:00+00:00',
        'status' => 1,
        'role' => 'Admin',
        'profile' => ['name' => 'Alice'],
    ]);

    $array = $dto->toEloquent();

    expect($array['registeredAt'])->toBe('2024-01-15 10:00:00');
});

// Invalid type

it('throws on invalid type', function () {
    AccountDTO::fromArray([
        'id' => 'not-a-number',
        'balance' => 0.0,
        'isActive' => false,
        'tags' => [],
    ]);
})->throws(InvalidArgumentException::class, 'Invalid type');

// with() preserves enum and DateTime types

it('preserves enum and datetime types when using with()', function () {
    $original = ContactDTO::fromArray([
        'email' => 'test@example.com',
        'registeredAt' => '2024-01-15T10:00:00+00:00',
        'status' => 1,
        'role' => 'Admin',
        'profile' => ['name' => 'Alice'],
    ]);

    $updated = $original->with(['email' => 'new@example.com']);

    expect($updated->email)->toBe('new@example.com');
    expect($updated->status)->toBe(AccountStatus::Active);
    expect($updated->role)->toBe(UserRole::Admin);
});
