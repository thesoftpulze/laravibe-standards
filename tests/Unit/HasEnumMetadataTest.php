<?php

declare(strict_types=1);

use TheSoftPulze\LaravibeStandards\Tests\Fixtures\Priority;
use TheSoftPulze\LaravibeStandards\Tests\Fixtures\ToastType;

// options()

it('returns options for backed enum', function () {
    $options = ToastType::options();

    expect($options)->toBeArray();
    expect($options[0])->toHaveKeys(['name', 'value', 'label']);
    expect($options[0]['name'])->toBe('Error');
    expect($options[0]['value'])->toBe(1);
    expect($options[0]['label'])->toBe('Error');
});

it('returns options for unit enum', function () {
    $options = Priority::options();

    expect($options)->toBeArray();
    expect($options[0])->toHaveKeys(['name', 'value', 'label']);
    expect($options[0]['name'])->toBe('Low');
    expect($options[0]['value'])->toBe('Low');
    expect($options[0]['label'])->toBe('Low');
});

// values()

it('returns values for backed enum', function () {
    expect(ToastType::values())->toBe([1, 2, 3, 4]);
});

it('returns values for unit enum', function () {
    expect(Priority::values())->toBe(['Low', 'Medium', 'High']);
});

// names()

it('returns names for backed enum', function () {
    expect(ToastType::names())->toBe(['Error', 'Success', 'Warning', 'Info']);
});

// isValidValue()

it('validates backed enum value', function () {
    expect(ToastType::isValidValue(1))->toBeTrue();
    expect(ToastType::isValidValue(99))->toBeFalse();
});

it('validates unit enum value', function () {
    expect(Priority::isValidValue('Low'))->toBeTrue();
    expect(Priority::isValidValue('Invalid'))->toBeFalse();
});

// isValidName()

it('validates enum name', function () {
    expect(ToastType::isValidName('Error'))->toBeTrue();
    expect(ToastType::isValidName('Unknown'))->toBeFalse();
    expect(ToastType::isValidName('error'))->toBeTrue();
});

// fromValueOrFail()

it('finds case by backed value', function () {
    expect(ToastType::fromValueOrFail(1))->toBe(ToastType::Error);
});

it('finds case by unit value (name)', function () {
    expect(Priority::fromValueOrFail('Low'))->toBe(Priority::Low);
});

it('finds case by name as fallback for backed enum', function () {
    expect(ToastType::fromValueOrFail('Error'))->toBe(ToastType::Error);
});

it('throws for invalid value', function () {
    ToastType::fromValueOrFail(99);
})->throws(InvalidArgumentException::class);

// tryFromName()

it('finds case by name case-insensitively', function () {
    expect(ToastType::tryFromName('Error'))->toBe(ToastType::Error);
    expect(ToastType::tryFromName('error'))->toBe(ToastType::Error);
    expect(ToastType::tryFromName('Unknown'))->toBeNull();
});

it('finds unit enum by name', function () {
    expect(Priority::tryFromName('Medium'))->toBe(Priority::Medium);
    expect(Priority::tryFromName('unknown'))->toBeNull();
});

// label()

it('generates human-readable label from backed enum name', function () {
    expect(ToastType::Error->label())->toBe('Error');
    expect(ToastType::Success->label())->toBe('Success');
});

it('generates human-readable label with underscores', function () {
    expect(ToastType::Warning->label())->toBe('Warning');
});

// toOption()

it('returns option array for backed enum', function () {
    $option = ToastType::Error->toOption();

    expect($option)->toBe([
        'name' => 'Error',
        'value' => 1,
        'label' => 'Error',
    ]);
});

it('returns option array for unit enum', function () {
    $option = Priority::Low->toOption();

    expect($option)->toBe([
        'name' => 'Low',
        'value' => 'Low',
        'label' => 'Low',
    ]);
});
